#!/usr/bin/env python3

"""
verify_integrity.py

Verifies that every file listed in CHECKSUMS.sha256 exists and matches its SHA256.
Usage:
  python3 verify_integrity.py
Exit codes:
  0 OK
  1 Mismatch or missing file
"""
from __future__ import annotations

import hashlib
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent
CHECKSUMS = ROOT / "CHECKSUMS.sha256"


def sha256_file(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as f:
        for chunk in iter(lambda: f.read(1024 * 1024), b""):
            h.update(chunk)
    return h.hexdigest()


def main() -> int:
    if not CHECKSUMS.exists():
        print("❌ Missing CHECKSUMS.sha256")
        return 1

    ok = True
    lines = [ln.strip() for ln in CHECKSUMS.read_text(encoding="utf-8").splitlines() if ln.strip()]
    for ln in lines:
        try:
            expected, rel = ln.split(maxsplit=1)
            rel = rel.strip()
        except ValueError:
            print(f"❌ Bad line in CHECKSUMS.sha256: {ln}")
            ok = False
            continue

        path = ROOT / rel
        if not path.exists():
            print(f"❌ Missing: {rel}")
            ok = False
            continue

        actual = sha256_file(path)
        if actual != expected:
            print(f"❌ Mismatch: {rel}\n   expected: {expected}\n   actual:   {actual}")
            ok = False
        else:
            print(f"✅ OK: {rel}")

    return 0 if ok else 1


if __name__ == "__main__":
    raise SystemExit(main())
