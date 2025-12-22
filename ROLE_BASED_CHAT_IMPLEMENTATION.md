# Role-Based Chat System Implementation

## Overview

This implementation provides a role-based chat system using Chatify with Laravel Reverb, enforcing access control at the backend level to ensure users can only communicate with authorized contacts based on their roles.

## User Roles

1. **user** - Regular users (customers)
2. **admin_toko** - UMKM store owners/admins
3. **super_admin** - System administrators

## Chat Access Rules

### 1. Regular User (`user`)
- ✅ Can chat with: `admin_toko` of their assigned UMKM
- ✅ Can chat with: `super_admin`
- ❌ Cannot chat with: other `user` roles
- ❌ Cannot chat with: `admin_toko` from different UMKMs

### 2. Admin Toko (`admin_toko`)
- ✅ Can chat with: `user` roles assigned to their UMKM
- ✅ Can chat with: `super_admin`
- ❌ Cannot chat with: other `admin_toko` roles

### 3. Super Admin (`super_admin`)
- ✅ Can chat with: ALL roles (users, admin_toko, other super_admins)

## Database Structure

### Migration: `add_umkm_id_to_users_table`

Adds a nullable foreign key `umkm_id` to the `users` table to link regular users to a specific UMKM:

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('umkm_id')
          ->nullable()
          ->after('role')
          ->constrained('umkms')
          ->onDelete('set null');
});
```

**Purpose**: This field allows regular users to be associated with a specific UMKM, enabling the system to determine which `admin_toko` they can communicate with.

## Implementation Components

### 1. User Model (`app/Models/User.php`)

#### Added Fields
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'umkm_id', // New field
];
```

#### New Relationships
```php
// For regular users - their assigned UMKM
public function assignedUmkm()
{
    return $this->belongsTo(Umkm::class, 'umkm_id');
}
```

#### Authorization Methods

**`canChatWith(User $targetUser): bool`**
- Core authorization logic
- Checks if current user can communicate with target user
- Enforces all role-based rules

**`getAllowedChatUsers()`**
- Returns collection of users the current user can chat with
- Used for filtering contact lists and search results
- Implements role-specific query logic

### 2. ChatPolicy (`app/Policies/ChatPolicy.php`)

Laravel policy for chat authorization:

```php
public function sendMessage(User $user, User $recipient): bool
{
    return $user->canChatWith($recipient);
}

public function viewConversation(User $user, User $otherUser): bool
{
    return $user->canChatWith($otherUser);
}
```

**Purpose**: Provides gate-based authorization that can be used throughout the application.

### 3. ChatifyRoleMiddleware (`app/Http/Middleware/ChatifyRoleMiddleware.php`)

Middleware that validates all Chatify requests:

**Key Features:**
- Intercepts all requests involving another user ID
- Validates authorization before processing
- Returns 403 error for unauthorized access
- Works with both JSON and regular requests

**Applied to**: All Chatify routes via `chatify.role` middleware alias

### 4. Custom MessagesController (`app/Http/Controllers/Chatify/MessagesController.php`)

Extends Chatify's base controller to add role-based filtering:

#### Overridden Methods:

**`getContacts(Request $request)`**
- Filters contact list to show only allowed users
- Uses `getAllowedChatUsers()` method
- Returns formatted contact data with avatars

**`fetch(Request $request)`**
- Validates conversation access before fetching messages
- Prevents unauthorized message viewing
- Returns 403 error if access denied

**`send(Request $request)`**
- Validates sender can message recipient
- Prevents unauthorized message sending
- Returns 403 error if access denied

**`search(Request $request)`**
- Filters search results to allowed users only
- Ensures users can't discover unauthorized contacts
- Returns only accessible users

**`idFetchData(Request $request)`**
- Validates access to user info
- Prevents information disclosure

### 5. Route Configuration (`routes/chatify/web.php`)

Updated to use custom controller:

```php
use App\Http\Controllers\Chatify\MessagesController;

Route::get('/', [MessagesController::class, 'index']);
Route::post('/sendMessage', [MessagesController::class, 'send']);
Route::post('/fetchMessages', [MessagesController::class, 'fetch']);
Route::get('/getContacts', [MessagesController::class, 'getContacts']);
// ... all other routes
```

### 6. Chatify Configuration (`config/chatify.php`)

Added middleware to routes:

```php
'routes' => [
    'middleware' => ['web', 'auth', 'chatify.role'], // Added chatify.role
],
```

### 7. Middleware Registration (`bootstrap/app.php`)

Registered middleware alias:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
    'chatify.role' => \App\Http\Middleware\ChatifyRoleMiddleware::class,
]);
```

## Security Considerations

### Backend Enforcement
- ✅ All restrictions enforced at backend level
- ✅ Authorization checks in controller methods
- ✅ Middleware validation on all routes
- ✅ Policy-based authorization available

### Data Protection
- ✅ Users cannot access conversations they're not part of
- ✅ Contact lists filtered server-side
- ✅ Search results restricted by authorization
- ✅ User info protected from unauthorized access

### Database Integrity
- ✅ Foreign key constraints maintain referential integrity
- ✅ Minimal schema changes (single column addition)
- ✅ Cascading deletes handled properly (SET NULL)

## Usage Examples

### Assigning a User to UMKM

```php
// When creating/updating a regular user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role' => User::ROLE_USER,
    'umkm_id' => 1, // Assign to UMKM with ID 1
]);
```

### Checking Chat Permission

```php
$currentUser = auth()->user();
$targetUser = User::find($targetId);

if ($currentUser->canChatWith($targetUser)) {
    // Allow chat
} else {
    // Deny access
}
```

### Getting Allowed Contacts

```php
$allowedContacts = auth()->user()->getAllowedChatUsers();
```

### Using Policy in Controllers

```php
use Illuminate\Support\Facades\Gate;

if (Gate::allows('sendMessage', [$currentUser, $recipient])) {
    // Send message
}
```

## Testing Scenarios

### Test Case 1: Regular User Restrictions
1. Create user with `role='user'` and `umkm_id=1`
2. Try to chat with `admin_toko` who owns UMKM 1 → ✅ Should work
3. Try to chat with `admin_toko` who owns UMKM 2 → ❌ Should fail
4. Try to chat with another `user` → ❌ Should fail
5. Try to chat with `super_admin` → ✅ Should work

### Test Case 2: Admin Toko Restrictions
1. Create `admin_toko` owning UMKM 1
2. Try to chat with `user` having `umkm_id=1` → ✅ Should work
3. Try to chat with `user` having `umkm_id=2` → ❌ Should fail
4. Try to chat with another `admin_toko` → ❌ Should fail
5. Try to chat with `super_admin` → ✅ Should work

### Test Case 3: Super Admin Access
1. Create `super_admin`
2. Try to chat with any `user` → ✅ Should work
3. Try to chat with any `admin_toko` → ✅ Should work
4. Try to chat with another `super_admin` → ✅ Should work

## Migration Steps

1. **Run Migration**:
   ```bash
   php artisan migrate
   ```

2. **Clear Config Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Update Existing Users**:
   ```php
   // Assign regular users to UMKMs
   User::where('role', 'user')->update(['umkm_id' => 1]); // Update as needed
   ```

## Maintenance Notes

### Adding New Routes
- All new Chatify routes must use `MessagesController` from `App\Http\Controllers\Chatify`
- Routes will automatically inherit middleware protection

### Modifying Authorization Logic
- Update `canChatWith()` method in User model
- Update `getAllowedChatUsers()` method in User model
- Policy will automatically use updated logic

### Core Chatify Files
- ✅ No core Chatify package files were modified
- ✅ Extension through inheritance (MessagesController extends ChatifyMessagesController)
- ✅ Safe for Chatify package updates

## Advantages of This Approach

1. **Backend Security**: All authorization happens server-side
2. **Clean Code**: Uses Laravel's native authorization features
3. **Maintainable**: Clear separation of concerns
4. **Minimal Changes**: Only adds what's necessary
5. **Upgrade Safe**: Doesn't modify vendor files
6. **Flexible**: Easy to adjust rules by modifying User model methods
7. **Production Ready**: Includes proper error handling and validation

## Future Enhancements

Potential improvements (not currently implemented):

1. **Group Chat**: Support for multi-user conversations
2. **Message Archival**: Automatic archival of old conversations
3. **Read Receipts**: Role-specific read receipt handling
4. **File Attachments**: Role-based file size/type restrictions
5. **Notifications**: Role-specific notification preferences
6. **Analytics**: Track chat usage by role
7. **Rate Limiting**: Prevent spam by rate-limiting messages

## Troubleshooting

### Users Can't See Contacts
- Check `umkm_id` is set for regular users
- Verify `admin_toko` owns the UMKM
- Clear cache: `php artisan cache:clear`

### Messages Not Sending
- Check recipient is in allowed users list
- Verify middleware is registered
- Check Laravel logs for errors

### Search Not Working
- Ensure search method is overridden in custom controller
- Verify routes use custom controller
- Check middleware is not blocking requests

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true` in `.env`
3. Verify database migrations: `php artisan migrate:status`
4. Check middleware registration: `php artisan route:list`
