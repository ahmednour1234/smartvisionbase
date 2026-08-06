import os
import textwrap

BASE = os.path.abspath(os.path.dirname(__file__))

def ensure_dirs(paths):
    for p in paths:
        os.makedirs(os.path.join(BASE, p), exist_ok=True)

def write(rel_path: str, content: str):
    path = os.path.join(BASE, rel_path)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, "w", encoding="utf-8") as f:
        f.write(textwrap.dedent(content).strip() + "\n")
    print("OK:", rel_path)

print("This ZIP already contains generated files. build.py is optional.")
