import sqlite3
from pathlib import Path
from datetime import datetime, date

source = Path("database/database.sqlite")
target = Path("db-mysql.sql")

if not source.exists():
    raise SystemExit(f"Database tidak ditemukan: {source}")

conn = sqlite3.connect(source)
conn.row_factory = sqlite3.Row

# Tabel aplikasi dan data referensi yang dibutuhkan.
tables = ["migrations", "users", "articles", "cities", "kecamatans"]

def mysql_type(col_type, pk=False):
    t = (col_type or "").upper()
    if pk:
        return "BIGINT NOT NULL AUTO_INCREMENT"
    if "INT" in t or t == "TINYINT(1)":
        return "BIGINT"
    if "CHAR" in t or "CLOB" in t or "TEXT" in t:
        return "LONGTEXT" if t == "TEXT" else "VARCHAR(255)"
    if "REAL" in t or "FLOA" in t or "DOUB" in t:
        return "DOUBLE"
    if "BLOB" in t:
        return "LONGBLOB"
    if "DATE" in t or "TIME" in t:
        return "DATETIME"
    return "VARCHAR(255)"

def quote(value):
    if value is None:
        return "NULL"
    if isinstance(value, (int, float)):
        return str(value)
    if isinstance(value, bytes):
        return "0x" + value.hex()
    s = str(value).replace("\\", "\\\\").replace("\0", "\\0")
    s = s.replace("'", "''")
    return "'" + s + "'"

out = [
    "-- Export SQLite ke MySQL/MariaDB",
    "SET NAMES utf8mb4;",
    "SET FOREIGN_KEY_CHECKS=0;",
]

# Struktur tabel
for table in tables:
    exists = conn.execute(
        "SELECT 1 FROM sqlite_master WHERE type='table' AND name=?", (table,)
    ).fetchone()
    if not exists:
        print(f"Lewati tabel yang tidak ada: {table}")
        continue

    info = conn.execute(f'PRAGMA table_info("{table}")').fetchall()
    pk_cols = [r["name"] for r in info if r["pk"]]
    cols = []

    for r in info:
        name, typ, notnull, default, pk = (
            r["name"], r["type"], r["notnull"], r["dflt_value"], r["pk"]
        )
        is_auto_pk = bool(pk and len(pk_cols) == 1 and "INT" in (typ or "").upper())
        definition = f"`{name}` {mysql_type(typ, is_auto_pk)}"
        if notnull and not is_auto_pk:
            definition += " NOT NULL"
        if default is not None and not is_auto_pk:
            d = str(default)
            if d.upper() == "CURRENT_TIMESTAMP":
                definition += " DEFAULT CURRENT_TIMESTAMP"
            else:
                definition += f" DEFAULT {d}"
        cols.append(definition)

    if pk_cols:
        cols.append("PRIMARY KEY (" + ", ".join(f"`{c}`" for c in pk_cols) + ")")

    if table == "kecamatans":
        cols.append("KEY `kecamatans_city_id_index` (`city_id`)")

    out.append(f"DROP TABLE IF EXISTS `{table}`;")
    out.append(
        f"CREATE TABLE `{table}` (\n  " + ",\n  ".join(cols) +
        "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    )

# Data
for table in tables:
    exists = conn.execute(
        "SELECT 1 FROM sqlite_master WHERE type='table' AND name=?", (table,)
    ).fetchone()
    if not exists:
        continue
    rows = conn.execute(f'SELECT * FROM "{table}"').fetchall()
    columns = [r["name"] for r in conn.execute(f'PRAGMA table_info("{table}")')]
    for row in rows:
        names = ", ".join(f"`{c}`" for c in columns)
        values = ", ".join(quote(row[c]) for c in columns)
        out.append(f"INSERT INTO `{table}` ({names}) VALUES ({values});")

# Foreign key kecamatans -> cities
out += [
    "ALTER TABLE `kecamatans` ADD CONSTRAINT `kecamatans_city_id_foreign` "
    "FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;"
    if conn.execute(
        "SELECT 1 FROM sqlite_master WHERE type='table' AND name='kecamatans'"
    ).fetchone() and conn.execute(
        "SELECT 1 FROM sqlite_master WHERE type='table' AND name='cities'"
    ).fetchone() else "",
    "SET FOREIGN_KEY_CHECKS=1;",
]
out = [line for line in out if line]

target.write_text("\n\n".join(out) + "\n", encoding="utf-8")
print(f"Berhasil membuat {target} ({target.stat().st_size:,} bytes)")
print("Tabel yang diekspor:", ", ".join(tables))
conn.close()
