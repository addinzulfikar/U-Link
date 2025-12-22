# Role-Based Chat Quick Reference

## Quick Setup

```bash
# Run the migration
php artisan migrate

# Clear caches
php artisan config:clear
php artisan cache:clear
```

## Role Chat Matrix

| User Role     | Can Chat With                          | Cannot Chat With                |
|---------------|----------------------------------------|---------------------------------|
| `user`        | • Their UMKM's `admin_toko`<br>• `super_admin` | • Other `user`<br>• Other UMKMs' `admin_toko` |
| `admin_toko`  | • Users with their `umkm_id`<br>• `super_admin` | • Other `admin_toko`            |
| `super_admin` | • Everyone                             | None                            |

## Key Files Modified

```
database/migrations/
  └── 2025_12_22_012724_add_umkm_id_to_users_table.php  [NEW]

app/Models/
  └── User.php                                           [MODIFIED]

app/Http/Controllers/Chatify/
  └── MessagesController.php                             [NEW]

app/Http/Middleware/
  └── ChatifyRoleMiddleware.php                          [NEW]

app/Policies/
  └── ChatPolicy.php                                     [NEW]

app/Providers/
  └── AppServiceProvider.php                             [MODIFIED]

bootstrap/
  └── app.php                                            [MODIFIED]

config/
  └── chatify.php                                        [MODIFIED]

routes/chatify/
  └── web.php                                            [MODIFIED]
```

## Authorization Flow

```
User sends chat message
         ↓
ChatifyRoleMiddleware checks target user ID
         ↓
Custom MessagesController::send()
         ↓
Validates: $currentUser->canChatWith($recipient)
         ↓
If authorized → Send message
If unauthorized → Return 403 error
```

## Code Examples

### Assign User to UMKM
```php
$user->update(['umkm_id' => 1]);
```

### Check Permission
```php
if ($user->canChatWith($otherUser)) {
    // Allowed
}
```

### Get Allowed Contacts
```php
$contacts = $user->getAllowedChatUsers();
```

## Testing Commands

```bash
# Check routes
php artisan route:list | grep chatify

# Test with Tinker
php artisan tinker
>>> $user = User::find(1);
>>> $target = User::find(2);
>>> $user->canChatWith($target);
>>> $user->getAllowedChatUsers();
```

## Security Features

✅ Backend-enforced authorization  
✅ Middleware validation on all routes  
✅ Policy-based access control  
✅ Filtered contact lists  
✅ Protected search results  
✅ Conversation access validation  
✅ No vendor file modifications  

## Common Issues

**Problem**: Users can't see their admin  
**Solution**: Ensure user's `umkm_id` matches admin's UMKM

**Problem**: Messages not sending  
**Solution**: Check recipient is in allowed users list

**Problem**: Routes not found  
**Solution**: Clear config cache with `php artisan config:clear`
