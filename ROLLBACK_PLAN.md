# Rollback Plan: Status Migration "scheduled" → "in_progress"

**Created:** January 9, 2026
**Migration Session:** 237-feat-migrate-status-from-scheduled-to-in-progress
**Original Commit:** d10b4eb

---

## Emergency Rollback Procedures

### Scenario 1: Issues Found During Code Changes (Phase 1)

**Symptoms:**
- Application errors after file modifications
- Tests failing
- Features broken

**Immediate Actions:**
```bash
# Option A: Revert to last known good commit
git log --oneline -10  # Find the commit before migration started
git reset --hard d10b4eb  # Replace with actual commit hash

# Option B: Revert specific commits
git revert HEAD  # Revert last commit
git revert <commit-hash>  # Revert specific problematic commit

# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

**Verification:**
```bash
# Run test suite
php artisan test

# Check application loads
php artisan serve
# Visit: http://localhost:8000
```

---

### Scenario 2: Issues Found After Database Migration (Phase 3)

**Symptoms:**
- Wrong status counts in dashboards
- Missing or incorrect request statuses
- Data integrity issues

**CRITICAL - Have Database Backup Ready:**

#### Option A: Restore from Backup Table
```sql
-- If backup table was created during Phase 3
UPDATE instruction_requests ir
INNER JOIN instruction_requests_backup_scheduled_migration backup
  ON ir.id = backup.id
SET ir.status = 'scheduled',
    ir.updated_at = backup.updated_at
WHERE backup.status = 'scheduled';

-- Verify restoration
SELECT COUNT(*) as scheduled_count
FROM instruction_requests
WHERE status = 'scheduled';
-- Should match original pre-migration count
```

#### Option B: Restore from SQL Dump
```bash
# Restore entire instruction_requests table
mysql -u [username] -p [database_name] < backup_instruction_requests_20260109_pre_migration.sql

# Or restore entire database if needed
mysql -u [username] -p [database_name] < full_backup_20260109.sql
```

**Post-Restore Actions:**
```bash
# Clear application caches
php artisan cache:clear
php artisan config:clear

# Verify data
php artisan tinker
>>> InstructionRequests::where('status', 'scheduled')->count();
>>> InstructionRequests::where('status', 'in_progress')->count();
>>> exit

# Test application
php artisan serve
```

---

### Scenario 3: Production Emergency (Critical System Failure)

**If migration deployed to production and causes critical issues:**

#### Immediate Hotfix SQL (Use with Caution)
```sql
-- Emergency: Revert recent "in_progress" back to "scheduled"
-- ONLY USE IF YOU'RE CERTAIN ABOUT THE DATE

UPDATE instruction_requests
SET status = 'scheduled'
WHERE status = 'in_progress'
  AND updated_at >= '2026-01-09 00:00:00';  -- Adjust to actual migration date/time

-- Verify
SELECT status, COUNT(*)
FROM instruction_requests
GROUP BY status;
```

#### Code Rollback
```bash
# Checkout previous stable tag/commit
git checkout tags/v1.0.0  # Replace with actual stable version
# Or
git checkout d10b4eb  # Original commit before migration

# Deploy old code
# (Follow your normal deployment process)
```

---

## Prevention Checklist

**Before Each Phase:**
- [ ] Git commit shows all changes clearly
- [ ] Can identify exact commits to revert
- [ ] Database backup is current
- [ ] Backup verification completed
- [ ] Test suite passes
- [ ] Manual testing completed

**Never Proceed If:**
- ❌ Tests are failing
- ❌ Application has errors in logs
- ❌ Database backup is missing or corrupted
- ❌ Not confident in rollback procedures

---

## Recovery Verification Steps

After any rollback, verify:

```bash
# 1. Application loads without errors
php artisan serve
# Visit all major pages

# 2. Database state is correct
php artisan tinker
>>> InstructionRequests::groupBy('status')->selectRaw('status, count(*) as count')->get();

# 3. No errors in logs
tail -f storage/logs/laravel.log

# 4. PowerGrid tables work
# - Visit /instruction-requests
# - Filter by status
# - Verify counts

# 5. Edit functionality works
# - Open a request
# - Change status
# - Save successfully
```

---

## Contact Information

**Documentation Location:**
- Implementation Plan: `/Users/gustavo.lanzas/basic-memory/instruction-requests/implementation/`
- Progress Tracker: `Status Migration Progress Tracker.md`
- Analysis: `Status Migration - Scheduled to In-Progress Analysis FINAL.md`

**CodeMCP Session:** 237-feat-migrate-status-from-scheduled-to-in-progress

---

## Post-Incident Actions

If rollback was necessary:

1. **Document What Went Wrong:**
   - Update Progress Tracker with issue details
   - Create Basic Memory note about the problem
   - Identify root cause

2. **Review and Revise:**
   - Update implementation plan to address issue
   - Add additional testing steps
   - Increase safety measures

3. **Test Fix Thoroughly:**
   - Don't retry migration until issue fully understood
   - Add specific test cases for the failure scenario
   - Consider smaller incremental steps

4. **Communication:**
   - If affects production: notify stakeholders
   - Document lessons learned
   - Update team on resolution

---

## Success Criteria (When to NOT Rollback)

Migration is successful when ALL are true:
- ✅ All automated tests pass
- ✅ All manual testing scenarios complete
- ✅ Dashboards show correct counts
- ✅ PowerGrid filters work correctly
- ✅ Edit forms display properly
- ✅ No errors in application logs
- ✅ Database verification queries all pass
- ✅ User acceptance testing successful (if applicable)

---

**Remember:** When in doubt, rollback and investigate. Better safe than sorry.
