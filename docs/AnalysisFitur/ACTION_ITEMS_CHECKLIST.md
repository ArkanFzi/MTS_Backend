# MTS Backend - Action Items & Fix Checklist

**Created:** June 6, 2026  
**Status:** 🔴 CRITICAL - 3 Issues Blocking Production  
**Priority:** IMMEDIATE

---

## 🔴 CRITICAL ISSUES (MUST FIX)

### [ ] Issue #1: PostController Missing myPosts() Method
**Priority:** 🔴 CRITICAL  
**File:** `Modules/User/F16_Post/Controllers/PostController.php`  
**Estimated Time:** 5 minutes  
**Blocking:** Feature F16, Route `/api/me/posts`

#### Steps to Fix:
1. Open file: `Modules/User/F16_Post/Controllers/PostController.php`
2. Add this method after `destroy()`:
```php
public function myPosts(): JsonResponse
{
    $posts = $this->service->getUserPosts(auth()->id());
    return response()->json([
        'success' => true,
        'data' => $posts
    ]);
}
```
3. Verify PostService has `getUserPosts()` method
4. Test endpoint: `GET /api/me/posts` (requires auth token)

**Verification:**
```bash
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/me/posts
# Expected: 200 OK with user's posts
```

---

### [ ] Issue #2: Create UserSuspension Model
**Priority:** 🔴 CRITICAL  
**Files to Create:** 
- `app/Models/Moderation/UserSuspension.php`
- Migration file

**Estimated Time:** 15 minutes  
**Blocking:** Feature F15, Ban/Unban system

#### Steps to Fix:

##### Step 2a: Create Migration
```bash
php artisan make:migration create_user_suspensions_table
```

Edit the migration file (find in `database/migrations/`):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_suspensions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('moderator_id');
            $table->string('reason');
            $table->dateTime('suspended_at');
            $table->dateTime('expires_at')->nullable();
            $table->string('status'); // permanent, temporary, appealed
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_suspensions');
    }
};
```

##### Step 2b: Create Model
Create file: `app/Models/Moderation/UserSuspension.php`
```php
<?php

namespace App\Models\Moderation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class UserSuspension extends Model
{
    use HasUuids;

    protected $table = 'user_suspensions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'moderator_id',
        'reason',
        'suspended_at',
        'expires_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'suspended_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function isActive(): bool
    {
        if ($this->status === 'permanent') {
            return true;
        }
        if ($this->expires_at && now() < $this->expires_at) {
            return true;
        }
        return false;
    }
}
```

##### Step 2c: Run Migration
```bash
php artisan migrate
# Verify table was created: user_suspensions
```

#### Verification:
```bash
# Check table exists
php artisan db:show user_suspensions

# Test model
php artisan tinker
> \App\Models\Moderation\UserSuspension::count()
# Expected: 0 (or number of suspensions if data exists)
```

---

### [ ] Issue #3: Create Permission & RolePermission Models
**Priority:** 🔴 CRITICAL  
**Files to Create:**
- `app/Models/Auth/Permission.php`
- `app/Models/Auth/RolePermission.php`
- Two migration files

**Estimated Time:** 30 minutes  
**Blocking:** Feature F8, Roles & Permissions system

#### Steps to Fix:

##### Step 3a: Create Migrations
```bash
php artisan make:migration create_permissions_table
php artisan make:migration create_role_permissions_table
```

Edit first migration (permissions):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('guard_name')->default('api');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
```

Edit second migration (role_permissions):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->timestamps();
            
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
```

##### Step 3b: Create Models
Create file: `app/Models/Auth/Permission.php`
```php
<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    use HasUuids;

    protected $table = 'permissions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'guard_name'
    ];

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }
}
```

Create file: `app/Models/Auth/RolePermission.php`
```php
<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasUuids;

    protected $table = 'role_permissions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
```

##### Step 3c: Update Role Model
Edit `app/Models/Auth/Role.php` and add:
```php
// Add to relations section
public function permissions(): HasMany
{
    return $this->hasMany(RolePermission::class);
}
```

##### Step 3d: Run Migrations
```bash
php artisan migrate
# Verify tables were created: permissions, role_permissions
```

#### Verification:
```bash
# Check tables exist
php artisan db:show permissions
php artisan db:show role_permissions

# Test models
php artisan tinker
> \App\Models\Auth\Permission::count()
> \App\Models\Auth\RolePermission::count()
```

---

## 🟡 MEDIUM PRIORITY ITEMS (SHOULD FIX)

### [ ] Add Leaderboard Repository
**Priority:** 🟡 MEDIUM  
**File to Create:** `Modules/User/F27_GamificationLeaderboard/Repositories/ReputationRepository.php`  
**Estimated Time:** 20 minutes

Extract repository methods from GamificationService to maintain consistency.

---

### [ ] Fix Feature Numbering Documentation
**Priority:** 🟡 MEDIUM  
**Files to Update:**
- `docs/StructureBackend/01_FEATURES_OVERVIEW.md`
- `docs/StructureBackend/04_FEATURE_MAPPING.md`

**Issue:** Moderator features are numbered F13, F14, F15 in code but docs expect F12, F13, F14

**Options:**
1. Rename folders: F13→F12, F14→F13, F15→F14
2. Update docs to match current numbering
3. Recommendation: Update docs (less risky)

---

### [ ] Clarify F8 & F9 Responsibilities
**Priority:** 🟡 MEDIUM  
**Files:** 
- `Modules/Admin/F8_RoleAndPermission/`
- `Modules/Admin/F9_UserManagement/`

**Issue:** Both modules handle user-related operations with potential overlap

**Resolution Options:**
1. Merge into single module
2. Clarify distinct responsibilities:
   - F8: Role/Permission management
   - F9: User profile/account management
3. Update documentation

---

## ✅ TESTING CHECKLIST

After fixing critical issues, verify:

### [ ] Test Issue #1 Fix (myPosts method)
```bash
# Get auth token first
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test endpoint with token
curl -H "Authorization: Bearer {TOKEN}" \
  http://localhost:8000/api/me/posts
# Expected: 200 OK with array of user's posts
```

### [ ] Test Issue #2 Fix (UserSuspension model)
```php
php artisan tinker
> \App\Models\Moderation\UserSuspension::create([
    'user_id' => '...',
    'moderator_id' => '...',
    'reason' => 'Spam',
    'suspended_at' => now(),
    'expires_at' => now()->addDays(7),
    'status' => 'temporary'
]);
# Expected: Record created successfully
```

### [ ] Test Issue #3 Fix (Permission models)
```php
php artisan tinker
> \App\Models\Auth\Permission::create(['name' => 'posts.create']);
> \App\Models\Auth\RolePermission::create([
    'role_id' => '...',
    'permission_id' => '...'
]);
# Expected: Records created successfully
```

---

## 📋 DEPLOYMENT VERIFICATION CHECKLIST

Before deploying, verify:

### Pre-Deployment
- [ ] All migrations run successfully
- [ ] All tests pass
- [ ] No console errors in logs
- [ ] Database backup created
- [ ] Staging environment tested

### Post-Deployment
- [ ] All endpoints responding (GET /api/health)
- [ ] Authentication working
- [ ] User can create posts
- [ ] User can fetch own posts (/api/me/posts)
- [ ] Ban system functional
- [ ] Permission system functional
- [ ] Leaderboard loading
- [ ] Notifications delivering

---

## 📊 PROGRESS TRACKER

```
Critical Issues:
├─ [ ] PostController@myPosts()          [5 min]
├─ [ ] UserSuspension Model              [15 min]
└─ [ ] Permission & RolePermission       [30 min]
   Total: ~50 minutes

Testing:
├─ [ ] Unit tests                        [2 hours]
├─ [ ] Integration tests                 [2 hours]
└─ [ ] Manual testing                    [1 hour]
   Total: ~5 hours

Documentation:
├─ [ ] Update feature numbering          [30 min]
├─ [ ] Create API documentation          [1 hour]
└─ [ ] Update README                     [30 min]
   Total: ~2 hours

Deployment:
├─ [ ] Staging deployment                [30 min]
├─ [ ] QA sign-off                       [4-8 hours]
└─ [ ] Production deployment             [1 hour]
   Total: ~5-9 hours

GRAND TOTAL: 12-21 hours
```

---

## 🔗 REFERENCE FILES

- [IMPLEMENTATION_ANALYSIS.md](IMPLEMENTATION_ANALYSIS.md) - Detailed 800+ line analysis
- [FEATURE_STATUS_QUICK_REFERENCE.md](FEATURE_STATUS_QUICK_REFERENCE.md) - Quick reference
- [FEATURE_IMPLEMENTATION_MATRIX.md](FEATURE_IMPLEMENTATION_MATRIX.md) - Visual matrix
- [ANALYSIS_SUMMARY.md](ANALYSIS_SUMMARY.md) - Executive summary

---

## 📞 QUESTIONS TO VERIFY

Before starting fixes, confirm:

1. [ ] Should Moderator features be renumbered to F12, F13, F14?
2. [ ] Should F8 & F9 be merged or kept separate?
3. [ ] Is the Permission/RolePermission structure optimal for your needs?
4. [ ] Any specific performance requirements for leaderboard queries?
5. [ ] Should UserSuspension be permanent, temporary, or both?

---

**Status:** Ready to implement  
**Estimated Completion:** 20-24 hours from start  
**Production Target:** 1-2 weeks

---

*Last Updated: June 6, 2026*
