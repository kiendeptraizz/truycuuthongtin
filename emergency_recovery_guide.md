# 🚨 EMERGENCY RECOVERY GUIDE

**⚠️ CRITICAL: Keep this guide accessible at all times**  
**📱 Print a copy and keep it near your workstation**

---

## 🆘 EMERGENCY CONTACT INFORMATION

### **System Administrator:**
- **Name:** [Your Name]
- **Phone:** [Your Phone Number]
- **Email:** [Your Email]
- **Backup Contact:** [Backup Person]

### **Technical Support:**
- **Laravel Documentation:** https://laravel.com/docs
- **Database Support:** [Your Database Provider]
- **Hosting Support:** [If applicable]

---

## 🚨 EMERGENCY SCENARIOS & IMMEDIATE ACTIONS

### **SCENARIO 1: DATABASE CORRUPTION DETECTED**

#### **🔴 IMMEDIATE ACTIONS (0-5 minutes):**
```bash
# 1. Stop all write operations
sudo systemctl stop apache2  # or nginx
# or kill Laravel processes if using artisan serve

# 2. Create emergency backup of current state
php artisan backup:automated --type=emergency --compress

# 3. Assess corruption level
php artisan emergency:assess-corruption

# 4. Check available backups
ls -la storage/backups/daily/ | tail -10
```

#### **🟡 RECOVERY ACTIONS (5-30 minutes):**
```bash
# 1. Find latest good backup
php artisan backup:find-latest-good

# 2. Perform recovery based on corruption level
# For LOW corruption:
php artisan emergency:recover --corruption-level=low

# For MEDIUM corruption:
php artisan emergency:recover --corruption-level=medium

# For HIGH corruption:
php artisan emergency:recover --corruption-level=high

# 3. Verify data integrity
php artisan db:health-check --comprehensive

# 4. Resume operations
sudo systemctl start apache2  # or nginx
```

#### **🟢 POST-RECOVERY (30+ minutes):**
- Document what happened
- Investigate root cause
- Update backup procedures if needed
- Review and improve monitoring

---

### **SCENARIO 2: COMPLETE DATA LOSS**

#### **🔴 IMMEDIATE ACTIONS:**
```bash
# 1. STOP ALL OPERATIONS IMMEDIATELY
sudo systemctl stop apache2
sudo systemctl stop mysql  # or your database service

# 2. Do NOT restart anything until recovery is complete

# 3. Find latest backup
ls -la storage/backups/daily/ | tail -5
ls -la storage/backups/weekly/ | tail -3

# 4. Choose best backup (most recent with good integrity)
php artisan backup:verify-integrity storage/backups/daily/backup_daily_YYYY_MM_DD_HH_MM_SS.enc
```

#### **🟡 FULL SYSTEM RECOVERY:**
```bash
# 1. Restore from backup
php artisan backup:restore storage/backups/daily/backup_daily_YYYY_MM_DD_HH_MM_SS.enc --verify

# 2. Verify all data relationships
php artisan db:health-check --comprehensive

# 3. Test critical functions
php artisan test:critical-functions

# 4. Start services
sudo systemctl start mysql
sudo systemctl start apache2

# 5. Verify system is working
curl http://localhost/admin/login
```

---

### **SCENARIO 3: BACKUP SYSTEM FAILURE**

#### **🔴 IMMEDIATE ACTIONS:**
```bash
# 1. Check backup system status
php artisan backup:status

# 2. Check storage space
df -h storage/backups

# 3. Check backup logs
tail -f storage/logs/backup.log

# 4. Create manual backup immediately
php artisan backup:manual --emergency
```

#### **🟡 RESOLUTION:**
```bash
# 1. Fix storage issues
# If storage full:
php artisan backup:cleanup --force
# or manually delete old backups

# 2. Fix permissions
chmod -R 755 storage/backups
chown -R www-data:www-data storage/backups

# 3. Test backup system
php artisan backup:test-system

# 4. Resume automated backups
# Check cron jobs are running
crontab -l | grep backup
```

---

### **SCENARIO 4: SYSTEM WON'T START**

#### **🔴 IMMEDIATE ACTIONS:**
```bash
# 1. Check Laravel logs
tail -f storage/logs/laravel.log

# 2. Check web server logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# 3. Check database connectivity
php artisan tinker
# Then run: DB::connection()->getPdo();

# 4. Check file permissions
ls -la storage/
ls -la bootstrap/cache/
```

#### **🟡 COMMON FIXES:**
```bash
# 1. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Fix permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# 3. Regenerate autoload
composer dump-autoload

# 4. Check environment
php artisan config:cache
php artisan route:cache
```

---

## 🛠️ RECOVERY COMMANDS REFERENCE

### **Backup Commands:**
```bash
# Create manual backup
php artisan backup:automated --type=manual --compress --verify

# List available backups
php artisan backup:list

# Verify backup integrity
php artisan backup:verify-integrity {backup_file}

# Find latest good backup
php artisan backup:find-latest-good
```

### **Recovery Commands:**
```bash
# Full system restore
php artisan backup:restore {backup_file} --verify

# Partial restore (specific tables)
php artisan backup:restore {backup_file} --tables=customers,customer_services

# Point-in-time restore
php artisan backup:restore {backup_file} --point-in-time="2025-07-23 14:30:00"

# Dry run (show what would be restored)
php artisan backup:restore {backup_file} --dry-run
```

### **Health Check Commands:**
```bash
# Full health check
php artisan db:health-check --comprehensive

# Quick health check
php artisan db:health-check

# Check specific issues
php artisan db:check-integrity
php artisan db:check-relationships
```

### **Emergency Commands:**
```bash
# Emergency recovery
php artisan emergency:recover --auto

# Assess corruption level
php artisan emergency:assess-corruption

# Emergency backup
php artisan backup:emergency
```

---

## 📋 PRE-RECOVERY CHECKLIST

Before starting any recovery procedure:

- [ ] **Stop all write operations** to prevent further damage
- [ ] **Document the current situation** (screenshots, error messages)
- [ ] **Identify the scope** of the problem (full system, specific tables, etc.)
- [ ] **Choose the appropriate backup** (most recent good backup)
- [ ] **Verify backup integrity** before using it
- [ ] **Notify stakeholders** about the situation and expected downtime
- [ ] **Have a rollback plan** in case recovery fails

---

## 📋 POST-RECOVERY CHECKLIST

After completing recovery:

- [ ] **Verify all data** is present and correct
- [ ] **Test critical functions** (login, customer creation, service assignment)
- [ ] **Check data relationships** (no orphaned records)
- [ ] **Verify recent transactions** are present
- [ ] **Test backup system** to ensure it's working
- [ ] **Document the incident** for future reference
- [ ] **Notify stakeholders** that system is restored
- [ ] **Monitor system closely** for the next 24 hours

---

## 🔍 TROUBLESHOOTING COMMON ISSUES

### **"Backup file not found" Error:**
```bash
# Check backup directory
ls -la storage/backups/
find storage/backups/ -name "*.enc" -type f

# Check if backup exists in subdirectories
find storage/backups/ -name "*backup*" -type f | head -10
```

### **"Decryption failed" Error:**
```bash
# Check encryption key
grep BACKUP_ENCRYPTION_KEY .env

# Try with different backup file
php artisan backup:list --verify
```

### **"Database connection failed" Error:**
```bash
# Check database service
sudo systemctl status mysql

# Check database credentials
grep DB_ .env

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### **"Permission denied" Error:**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Fix backup permissions
sudo chown -R www-data:www-data storage/backups/
sudo chmod -R 755 storage/backups/
```

---

## 📞 ESCALATION PROCEDURES

### **If recovery fails after 1 hour:**
1. **Contact backup technical support**
2. **Consider professional data recovery services**
3. **Prepare for business continuity procedures**
4. **Document all attempted recovery steps**

### **If data loss is confirmed:**
1. **Assess business impact**
2. **Notify customers if necessary**
3. **Implement manual processes if possible**
4. **Consider legal/compliance requirements**

---

## 🎯 PREVENTION MEASURES

### **Daily:**
- Monitor backup success notifications
- Check system health dashboard
- Verify critical functions are working

### **Weekly:**
- Test backup integrity
- Review backup logs
- Check storage space

### **Monthly:**
- Perform recovery drill
- Update emergency procedures
- Review and test all scenarios

---

**🚨 REMEMBER: When in doubt, STOP and seek help. It's better to take time to do recovery correctly than to cause additional damage by rushing.**

**📱 Keep this guide accessible offline in case of complete system failure.**
