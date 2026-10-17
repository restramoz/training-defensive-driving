<?php

namespace App\Services;

use App\Models\Article;
use App\Services\WhatsAppService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OllamaService
{
    protected ?string $apiKey;

    protected ?string $baseUrl;

    protected ?string $model;

    protected int $timeout;

    /**
     * Standard Training Defensive Driving core topics.
     *
     * @var array<int, array{name: string, focus: string}>
     */
    public const CORE_TOPICS = [
        [
            'name' => 'Prinsip Dasar Defensive Driving & Kesadaran Situasional Pengemudi',
            'focus' => 'Mentalitas berkendara aman, pemindaian visual 360 derajat, dan pengambilan keputusan di jalan raya',
        ],
        [
            'name' => 'Teknik Antisipasi Bahaya Lalu Lintas & Manajemen Jarak Aman',
            'focus' => 'Aturan jarak 3 detik, mengenali perilaku berbahaya pengguna jalan lain, dan rute pelarian darurat',
        ],
        [
            'name' => 'Pengendalian Kecepatan & Keselamatan Berkendara di Cuaca Buruk',
            'focus' => 'Mitigasi aquaplaning saat hujan lebat, visibilitas berkabut, jalan licin, dan teknik pengereman halus',
        ],
        [
            'name' => 'Pencegahan Distraksi Berkendara, Blind Spot & Manajemen Kelelahan',
            'focus' => 'Disiplin fokus tanpa ponsel, penyesuaian spion blind spot, serta manajemen istirahat pengemudi',
        ],
        [
            'name' => 'Budaya Keselamatan Berkendara untuk Pengemudi Armada Operasional Perusahaan',
            'focus' => 'Manajemen risiko berkendara armada niaga/logistik, SOP keselamatan kerja jalan, dan kepatuhan berlalu lintas',
        ],
        [
            'name' => 'Inspeksi Kendaraan Sebelum Berangkat & Tanggap Darurat Kerusakan di Jalan',
            'focus' => 'Pemeriksaan rutin ban, rem, lampu, cairan kendaraan, serta penanganan aman saat mogok di jalan raya',
        ],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.ollama.key');
        $this->baseUrl = config('services.ollama.base_url');
        $this->model = config('services.ollama.model');
        $this->timeout = (int) config('services.ollama.timeout', 120);
    }

    /**
     * Resolve chat endpoint URL safely.
     */
    public function getChatEndpoint(): string
    {
        $base = rtrim((string) $this->baseUrl, '/');

        if (str_ends_with($base, '/api/chat')) {
            return $base;
        }

        return "{$base}/api/chat";
    }

    /**
     * Select a relevant topic automatically based on region and existing articles.
     *
     * @return array{topic: string, focus: string}
     */
    public function pickTopicForRegion(string $cityName, ?string $citySlug = null): array
    {
        $existingTitles = [];
        if ($citySlug) {
            $existingTitles = Article::where('target_city_slug', $citySlug)
                ->pluck('title')
                ->toArray();
        }

        // Find first unused core topic
        foreach (self::CORE_TOPICS as $core) {
            $isUsed = false;
            foreach ($existingTitles as $title) {
                if (stripos($title, $core['name']) !== false) {
                    $isUsed = true;
                    break;
                }
            }

            if (! $isUsed) {
                return [
                    'topic' => "Panduan {$core['name']} di {$cityName}",
                    'focus' => $core['focus'],
                ];
            }
        }

        // If all core topics are covered, return an advanced operational angle
        $count = count($existingTitles) + 1;

        return [
            'topic' => "Strategi Pengemudi Bertanggung Jawab & Defensive Driving di {$cityName} (Bagian {$count})",
            'focus' => 'Pencegahan insiden lalu lintas komprehensif, evaluasi rute operasional, dan budaya keselamatan berkendara',
        ];
    }

    /**
     * Resolve word count from direct input (already validated as integer 300-3000).
     */
    public function resolveWordCount(int $wordCount): int
    {
        return $wordCount;
    }

    /**
     * Generate an article draft via Ollama.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function generateArticle(array $params): array
    {
        // 1. Verifikasi kelengkapan konfigurasi tanpa memanggil API
        if (empty($this->baseUrl)) {
            return [
                'success' => false,
                'error' => 'Konfigurasi OLLAMA_BASE_URL belum diatur di file .env.',
            ];
        }

        if (empty($this->model)) {
            return [
                'success' => false,
                'error' => 'Konfigurasi OLLAMA_MODEL belum diatur di file .env.',
            ];
        }

        $cityName = trim((string) ($params['city_name'] ?? 'Indonesia'));
        $districtName = ! empty($params['district_name']) ? trim((string) $params['district_name']) : null;
        $citySlug = $params['city_slug'] ?? null;
        $wordCount = $this->resolveWordCount((int) ($params['word_count'] ?? 6000));

        // Auto topic selection if not manually provided
        if (empty($params['topic'])) {
            $selected = $this->pickTopicForRegion($cityName, $citySlug);
            $topic = $selected['topic'];
            $focus = $selected['focus'];
        } else {
            $topic = trim((string) $params['topic']);
            $focus = $params['focus'] ?? 'Prinsip dasar Defensive Driving dan mitigasi risiko kecelakaan lalu lintas';
        }

        $locationContext = $districtName ? "Kecamatan {$districtName}, {$cityName}" : $cityName;

        $systemPrompt = <<<PROMPT
Anda adalah Lead Specialist Keselamatan Berkendara dan Instruktur Senior Defensive Driving di Indonesia.
Tugas Anda adalah menulis artikel edukatif dan otoritatif berstandar industri B2B dalam Bahasa Indonesia formal mengenai Training Defensive Driving untuk pengemudi individu dan armada perusahaan.

ATURAN KUALITAS & GAYA PENULISAN:
1. Nada bahasa: Profesional, lugas, mengalir alami, berbobot untuk pengambil keputusan (Fleet Manager, HSE Manager, HRD, Direktur Operasional, serta pengemudi profesional).
2. Dilarang menggunakan filler AI klise seperti "Di era modern saat ini", "Tak dapat dipungkiri", atau kalimat berulang.
3. Rujuk prinsip edukasi keselamatan jalan yang relevan di Indonesia: Undang-Undang No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, standar keselamatan kerja operasional armada, dan prinsip mitigasi risiko jalan raya.
4. Gunakan tag HTML semantik: <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <blockquote>, <table><thead><tr><th>...</th></tr></thead><tbody><tr><td>...</td></tr></tbody></table>.
5. Panjang target: Sekitar {$wordCount} kata.
6. Buat image_brief terpisah yang siap digunakan oleh desainer di Google Flow untuk menghasilkan gambar fotografi realistis beresolusi tinggi TANPA teks di gambar.

OUTPUT WAJIB:
Kembalikan HANYA dokumen JSON murni tanpa pembungkus markdown ```json ```.
Format skema JSON yang wajib dipatuhi:
{
  "title": "Judul Artikel Informatif dan Otoritatif",
  "slug": "slug-url-ramah-seo",
  "excerpt": "Ringkasan eksekutif 2-3 kalimat mengenai intisari artikel.",
  "content": "<p>Isi artikel lengkap dalam HTML semantik...</p>",
  "seo_title": "SEO Title Max 60 Karakter | Training Defensive Driving",
  "meta_description": "Meta description persuasif max 155 karakter.",
  "suggested_faqs": [
    {"q": "Pertanyaan 1...", "a": "Jawaban ringkas 1..."},
    {"q": "Pertanyaan 2...", "a": "Jawaban ringkas 2..."}
  ],
  "image_brief": {
    "concept": "Konsep visual foto dalam Bahasa Indonesia",
    "english_prompt": "Detailed descriptive photo prompt in English for Google Flow: realistic photography, professional driver demonstrating focus and defensive driving awareness, Indonesian road environment, natural lighting, high detail, no text overlays, 8k",
    "aspect_ratio": "16:9",
    "recommended_alt": "Alt text ramah SEO dalam Bahasa Indonesia"
  }
}
PROMPT;

        $userPrompt = <<<PROMPT
Silakan buat artikel Defensive Driving lengkap dengan rincian berikut:
- Topik Artikel: {$topic}
- Fokus Materi: {$focus}
- Lokasi Target: {$locationContext}
- Target Jumlah Kata: ~{$wordCount} kata (perkiraan, tidak jaminan pasti)

Keluarkan HANYA JSON valid.
PROMPT;

        try {
            $endpoint = $this->getChatEndpoint();

            $client = Http::timeout($this->timeout);

            // Jika API key tersedia, gunakan Bearer token (Ollama Cloud / remote proxy)
            if (! empty($this->apiKey)) {
                $client = $client->withToken($this->apiKey);
            }

            $response = $client->post($endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'stream' => false,
            ]);

            if (! $response->successful()) {
                // Log safe error without printing API key
                Log::error('Ollama API error response', [
                    'status' => $response->status(),
                    'endpoint' => $endpoint,
                    'model' => $this->model,
                ]);

                return [
                    'success' => false,
                    'error' => "Ollama merespons dengan status HTTP {$response->status()}: ".Str::limit($response->body(), 200),
                ];
            }

            $data = $response->json();
            $rawContent = $data['message']['content'] ?? '';

            if (empty(trim($rawContent))) {
                return [
                    'success' => false,
                    'error' => 'Ollama mengembalikan respons konten kosong.',
                ];
            }

            $result = $this->parseResponse($rawContent, $topic, $cityName, $wordCount);

            if (! ($result['success'] ?? false)) {
                return $result;
            }

            // Verify content is not empty
            if (empty(trim(strip_tags($result['content'] ?? '')))) {
                return [
                    'success' => false,
                    'error' => 'Malformed or empty article content is not accepted as a successful article.',
                ];
            }

            // Enforce word-count constraints before adding UI elements.
            // Callouts must not influence the word-count validation.
            $generatedWordCount = $this->countWords($result['content'] ?? '');
            if ($generatedWordCount > 9000) {
                return [
                    'success' => false,
                    'error' => "Generated article exceeds the maximum allowed 9.000 words (achieved: {$generatedWordCount}).",
                ];
            }

            // Attempt continuation if the article is shorter than requested (max 8 attempts)
            $maxAttempts = 8;
            $attempt = 0;
            while ($generatedWordCount < $wordCount && $attempt < $maxAttempts) {
                $remaining = $wordCount - $generatedWordCount;
                $continuation = $this->requestContinuation($result['content'], $remaining, $cityName, $topic);
                if (! ($continuation['success'] ?? false)) {
                    return $continuation;
                }
                $result['content'] .= $continuation['content'];
                $generatedWordCount = $this->countWords($result['content']);
                $attempt++;
            }

            if ($generatedWordCount < $wordCount) {
                return [
                    'success' => false,
                    'error' => "Generated article is shorter than the requested word count (achieved: {$generatedWordCount}, requested: {$wordCount}).",
                ];
            }

            $result['validated_word_count'] = $generatedWordCount;

            // After word-count validation passes, inject five WhatsApp callouts
            $result = $this->addWhatsAppCallout($result, $cityName);

            return $result;
        } catch (ConnectionException $e) {
            Log::error('Ollama connection timeout or unreachable: '.$e->getMessage());

            return [
                'success' => false,
                'error' => 'Koneksi ke Ollama gagal atau timeout setelah '.$this->timeout.' detik. Pastikan layanan Ollama aktif dan endpoint dapat dijangkau.',
            ];
        } catch (\Throwable $e) {
            Log::error('OllamaService Exception: '.$e->getMessage());

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan saat memproses permintaan artikel: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Robust JSON parsing and fallback structure.
     *
     * @return array<string, mixed>
     */
    public function parseResponse(string $rawContent, string $fallbackTopic, string $cityName, int $wordCount): array
    {
        $cleaned = trim($rawContent);

        if ($cleaned === '') {
            return [
                'success' => false,
                'error' => 'Malformed or empty article content is not accepted as a successful article.',
            ];
        }

        // Strip markdown code fences if present
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        } elseif (str_starts_with($cleaned, '```json')) {
            $cleaned = trim(substr($cleaned, 7));
            if (str_ends_with($cleaned, '```')) {
                $cleaned = trim(substr($cleaned, 0, -3));
            }
        } elseif (str_starts_with($cleaned, '```')) {
            $cleaned = trim(substr($cleaned, 3));
            if (str_ends_with($cleaned, '```')) {
                $cleaned = trim(substr($cleaned, 0, -3));
            }
        }

        $decoded = json_decode($cleaned, true);

        // If direct parse failed, attempt to extract JSON object with regex
        if (! is_array($decoded)) {
            if (preg_match('/\{[\s\S]*\}/', $cleaned, $extracted)) {
                $decoded = json_decode($extracted[0], true);
            }
        }

        if (is_array($decoded) && ! empty($decoded['title'])) {
            $articleContent = (string) ($decoded['content'] ?? '');
            if (trim(strip_tags($articleContent)) === '') {
                return [
                    'success' => false,
                    'error' => 'Malformed or empty article content is not accepted as a successful article.',
                ];
            }

            // Ensure structured image_brief is properly isolated
            $imageBrief = $decoded['image_brief'] ?? [];
            if (! is_array($imageBrief)) {
                $imageBrief = [];
            }

            $concept = $imageBrief['concept'] ?? "Sesi edukasi keselamatan Defensive Driving di {$cityName}";
            $englishPrompt = $imageBrief['english_prompt'] ?? 'Professional corporate photograph of defensive driving safety awareness session in Indonesia, focused professional driver at vehicle controls, realistic road setting, natural lighting, high resolution, no text overlay';
            $aspectRatio = $imageBrief['aspect_ratio'] ?? '16:9';
            $recommendedAlt = $imageBrief['recommended_alt'] ?? "Training Defensive Driving di {$cityName}";

            return [
                'success' => true,
                'title' => (string) $decoded['title'],
                'slug' => ! empty($decoded['slug']) ? Str::slug($decoded['slug']) : Str::slug($decoded['title']),
                'excerpt' => (string) ($decoded['excerpt'] ?? Str::limit(strip_tags($articleContent), 180)),
                'content' => $articleContent,
                'seo_title' => (string) ($decoded['seo_title'] ?? Str::limit($decoded['title'].' | Training Defensive Driving', 60)),
                'meta_description' => (string) ($decoded['meta_description'] ?? Str::limit($decoded['excerpt'] ?? '', 155)),
                'suggested_faqs' => is_array($decoded['suggested_faqs'] ?? null) ? $decoded['suggested_faqs'] : [],
                'image_brief' => [
                    'concept' => $concept,
                    'english_prompt' => $englishPrompt,
                    'aspect_ratio' => $aspectRatio,
                    'recommended_alt' => $recommendedAlt,
                ],
            ];
        }

        // Recover HTML content when the model returns imperfect JSON or raw text.
        $fallbackContent = $cleaned;

        if (preg_match('/"content"\s*:\s*("(?:\\\\.|[^"\\\\])*")/s', $cleaned, $match)) {
            $extracted = json_decode($match[1], true);
            if (is_string($extracted) && trim($extracted) !== '') {
                $fallbackContent = $extracted;
            }
        }

        if (trim(strip_tags($fallbackContent)) === '') {
            return [
                'success' => false,
                'error' => 'Malformed or empty article content is not accepted as a successful article.',
            ];
        }

        if (preg_match('/<\s*(p|h[1-6]|table|thead|tbody|tr|td|th|ul|ol|li|blockquote)\b/i', $fallbackContent)) {
            $fallbackHtml = $fallbackContent;
        } else {
            $fallbackHtml = '<p>'.nl2br(e($fallbackContent)).'</p>';
        }

        $slug = Str::slug($fallbackTopic);
        $excerpt = Str::limit(strip_tags($fallbackHtml), 180);

        return [
            'success' => true,
            'title' => $fallbackTopic,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $fallbackHtml,
            'seo_title' => Str::limit("{$fallbackTopic} | Training Defensive Driving", 60),
            'meta_description' => Str::limit("Panduan {$fallbackTopic} untuk kesadaran berkendara aman dan keselamatan armada di {$cityName}.", 155),
            'suggested_faqs' => [],
            'image_brief' => [
                'concept' => "Pelatihan defensive driving dan kesadaran pengemudi di {$cityName}",
                'english_prompt' => 'High quality corporate documentary photography of driver safety training in Indonesia, professional driver, road awareness briefing, authentic vehicle cabin, realistic lighting, 8k, no text',
                'aspect_ratio' => '16:9',
                'recommended_alt' => "Training Defensive Driving - {$fallbackTopic}",
            ],
            'raw' => $rawContent,
        ];
    }

    /**
     * Request a continuation from Ollama for the given partial content.
     * Returns an array with keys: success (bool) and content (string) on success.
     *
     * @return array{success: bool, content?: string, error?: string}
     */
    public function requestContinuation(string $existingContent, int $targetWords, string $cityName, ?string $topic = null): array
    {
        $topicContext = $topic ? "mengenai topik \"{$topic}\"" : 'Defensive Driving';
        $systemPrompt = "Anda adalah Lead Specialist Keselamatan Berkendara dan editor artikel Defensive Driving berbahasa Indonesia. Lanjutkan artikel yang diberikan {$topicContext} di {$cityName} dengan konten baru sekitar {$targetWords} kata. Jangan mengulang paragraf, judul, atau kalimat yang sudah ada. Output HANYA fragmen HTML valid menggunakan tag semantik seperti <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, dan <table> bila relevan. Dilarang mengeluarkan JSON, markdown code fence, komentar, atau metadata. Pastikan fragmen HTML bermakna dan berbobot.";
        $userPrompt = "Konten artikel yang sudah ada (gunakan sebagai konteks, jangan salin ulang):\n{$existingContent}\n\nTambahkan bagian lanjutan yang relevan dan substantif sebanyak kira-kira {$targetWords} kata. Fokus pada materi baru, studi kasus, teknik antisipasi bahaya di jalan raya, prosedur keselamatan armada, kepatuhan lalu lintas, dan tindakan preventif pengemudi. Kembalikan HANYA fragmen HTML tambahan, bukan artikel lengkap atau JSON.";

        try {
            $endpoint = $this->getChatEndpoint();
            $client = Http::timeout($this->timeout);
            if (! empty($this->apiKey)) {
                $client = $client->withToken($this->apiKey);
            }
            $response = $client->post($endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'stream' => false,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => 'Continuation request failed with HTTP '.$response->status()];
            }

            $data = $response->json();
            $raw = $data['message']['content'] ?? '';
            if (trim($raw) === '') {
                return ['success' => false, 'error' => 'Empty continuation response'];
            }

            // Strip markdown fences if returned
            $cleaned = trim($raw);
            if (preg_match('/^```(?:html|json)?\s*(.*?)\s*```$/s', $cleaned, $matches)) {
                $cleaned = trim($matches[1]);
            }

            // If model accidentally returned JSON, try to extract content field
            if (preg_match('/"content"\s*:\s*("(?:\\\\.|[^"\\\\])*")/s', $cleaned, $match)) {
                $extracted = json_decode($match[1], true);
                if (is_string($extracted) && trim($extracted) !== '') {
                    $cleaned = $extracted;
                }
            }

            if (empty(trim($cleaned))) {
                return ['success' => false, 'error' => 'Continuation parsing produced no content'];
            }

            $continuationContent = trim($cleaned);
            $addedWords = $this->countWords($continuationContent);

            if ($addedWords === 0) {
                return ['success' => false, 'error' => 'Continuation contains no usable words'];
            }

            // Reject identical/repeated content to avoid loop appending same text
            $normalizedExisting = preg_replace('/\s+/u', ' ', strip_tags($existingContent));
            $normalizedContinuation = preg_replace('/\s+/u', ' ', strip_tags($continuationContent));

            if (
                is_string($normalizedExisting)
                && is_string($normalizedContinuation)
                && trim($normalizedContinuation) !== ''
                && str_contains($normalizedExisting, trim($normalizedContinuation))
            ) {
                return ['success' => false, 'error' => 'Continuation repeated existing content'];
            }

            return ['success' => true, 'content' => $continuationContent];
        } catch (ConnectionException $e) {
            return ['success' => false, 'error' => 'Continuation connection error: '.$e->getMessage()];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Continuation exception: '.$e->getMessage()];
        }
    }

    /**
     * Add context-aware WhatsApp CTA callouts distributed across the article.
     * Inserts exactly five callouts after word-count validation passes.
     *
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    public function addWhatsAppCallout(array $result, string $cityName): array
    {
        $content = $this->sanitizeContent($result['content'] ?? '');

        // Prevent duplicate injection if pipeline runs multiple times
        if (str_contains($content, 'article-wa-card')) {
            $result['content'] = $content;

            return $result;
        }

        // Retrieve configured contacts; check services.whatsapp then training.whatsapp
        $rawContacts = config('services.whatsapp.callout_contacts')
            ?? config('training.whatsapp.callout_contacts', []);

        if (is_string($rawContacts)) {
            $rawContacts = array_filter(array_map('trim', explode(',', $rawContacts)));
        }

        if (! is_array($rawContacts) || empty($rawContacts)) {
            $result['content'] = $content;

            return $result; // Silently skip if no contacts configured (never fabricate numbers)
        }

        $waService = app(WhatsAppService::class);
        $validContacts = [];
        foreach ($rawContacts as $contact) {
            $normalized = $waService->normalize((string) $contact);
            if ($normalized !== null && ! in_array($normalized, $validContacts, true)) {
                $validContacts[] = $normalized;
            }
        }

        if (empty($validContacts)) {
            $result['content'] = $content;

            return $result; // No valid phone numbers available
        }

        // Prepare five contacts, randomizing selection across the valid contacts
        $shuffled = $validContacts;
        shuffle($shuffled);
        $selected = array_slice($shuffled, 0, min(4, count($shuffled)));
        while (count($selected) < 5) {
            $selected[] = $validContacts[array_rand($validContacts)];
        }
        shuffle($selected);

        // Split content by closing paragraph tags to insert between sections.
        $paragraphEnds = [];
        $offset = 0;
        while (($pos = stripos($content, '</p>', $offset)) !== false) {
            $paragraphEnds[] = $pos + 4; // Position immediately after </p>
            $offset = $pos + 4;
        }

        // Determine insertion indices – distribute proportionally across paragraphs.
        $insertPoints = [];
        $countEnds = count($paragraphEnds);
        for ($i = 0; $i < 5; $i++) {
            if ($countEnds === 0) {
                // No paragraphs found – append at the end.
                $insertPoints[] = null;
                continue;
            }
            $target = (int) round(($i + 1) * $countEnds / 6);
            $target = min($target, $countEnds - 1);
            $insertPoints[] = $paragraphEnds[$target];
        }

        $encodedCity = e($cityName);

        // Insert callouts in reverse order so character offsets remain valid.
        foreach (array_reverse($insertPoints, true) as $idx => $position) {
            $phone = $selected[$idx];
            $message = "Halo, saya ingin konsultasi mengenai program Training Defensive Driving untuk wilayah {$cityName}.";
            $waUrl = "https://wa.me/{$phone}?text=".rawurlencode($message);

            $ctaHtml = '<div class="article-wa-card not-prose" data-wa-callout="1" style="margin:1.75rem 0;padding:1.25rem 1.5rem;border-radius:14px;background-color:#EFF6FF;border:1px solid #BFDBFE;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">'
                .'<div class="article-card-body" style="flex:1 1 280px;">'
                .'<div class="article-card-badge" style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#1D4ED8;margin-bottom:0.25rem;">Konsultasi Training Defensive Driving &bull; '.$encodedCity.'</div>'
                .'<div class="article-card-heading" style="font-size:0.9375rem;font-weight:600;color:#0B1220;line-height:1.4;">Tingkatkan Keselamatan Berkendara Pengemudi &amp; Armada Perusahaan Anda</div>'
                .'<div class="article-card-subtext" style="font-size:0.8125rem;color:#475569;margin-top:0.25rem;line-height:1.4;">Diskusikan kebutuhan modul pelatihan, sertifikasi, dan jadwal in-house bersama instruktur kami.</div>'
                .'</div>'
                .'<a href="'.e($waUrl).'" target="_blank" rel="noopener noreferrer" class="article-wa-btn" style="display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.625rem 1.25rem;border-radius:10px;background-color:#25D366;color:#FFFFFF;font-weight:700;font-size:0.8125rem;text-decoration:none;white-space:nowrap;">'
                .'<span>Konsultasi via WhatsApp</span> &rarr;'
                .'</a>'
                .'</div>';

            if ($position !== null) {
                $content = substr_replace($content, $ctaHtml, $position, 0);
            } else {
                $content .= $ctaHtml;
            }
        }

        $result['content'] = $content;

        return $result;
    }

    /**
     * Count words in a string while ignoring HTML tags and decoding entities.
     * Excludes injected WhatsApp CTA callouts so word counts represent actual article body.
     *
     * @param  string  $content  HTML content
     * @return int Number of words
     */
    public function countWords(string $content): int
    {
        // Strip WhatsApp callout cards if present so word counting accurately measures article body
        $cleaned = preg_replace('/<div[^>]*class="[^"]*article-wa-card[^"]*"[\s\S]*?<\/a>\s*<\/div>/i', '', $content);
        if ($cleaned !== null) {
            $content = $cleaned;
        }

        // Remove HTML tags
        $text = strip_tags($content);
        // Decode HTML entities (e.g. &amp; -> &)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Normalize whitespace and split on word boundaries
        $words = preg_split('/\s+/u', trim($text));

        return $words === false ? 0 : count(array_filter($words, fn ($w) => $w !== ''));
    }

    public function sanitizeContent(string $content): string
    {
        // Replace LaTeX arrow patterns like "$ \rightarrow$" or "$ ightarrow$" with a proper arrow.
        $patterns = [
            '/\$\s*\\\\?rightarrow\s*\$/i',
            '/\$\s*ightarrow\s*\$/i',
        ];
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '→', $content);
        }

        // Remove any remaining stray dollar signs.
        return str_replace('$', '', $content);
    }
}
