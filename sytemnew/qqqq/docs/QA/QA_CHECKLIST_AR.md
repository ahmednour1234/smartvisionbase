# QA Checklist (Smart Vision CRM V12)

## Smoke
- Open `/admin/login` and login as Admin
- Open `/employee/login` and login as Sales user

## RBAC
- Sales cannot access `/admin`
- is_active=false cannot access any panel

## Company
- Create Arabic company names (should pass)
- Duplicate name (normalized) must be blocked by DB unique constraint

## Claim
- Two users claim same lead simultaneously: only one succeeds (atomic claim)

## Performance (Staging)
- Seed 100k companies (`SEED_PERFORMANCE=1`)
- Measure p95 for:
  - List Companies
  - My Portfolio
  - Search + Claim
