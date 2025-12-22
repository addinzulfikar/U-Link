# Role-Based Chat System - Visual Guide

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     U-LINK Chat System                           │
│                  Role-Based Authorization                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────┐        ┌─────────────┐        ┌─────────────┐
│    USER     │        │ ADMIN TOKO  │        │ SUPER ADMIN │
│  (Customer) │        │   (Owner)   │        │  (Platform) │
└──────┬──────┘        └──────┬──────┘        └──────┬──────┘
       │                      │                       │
       │ Can chat with:       │ Can chat with:        │ Can chat with:
       │ • Their admin        │ • Their users         │ • EVERYONE
       │ • Super admins       │ • Super admins        │
       │                      │                       │
       └──────────────────────┴───────────────────────┘


┌────────────────────────────────────────────────────────────────┐
│                    Authorization Flow                           │
└────────────────────────────────────────────────────────────────┘

    User Action (e.g., Send Message)
              ↓
    ┌─────────────────────┐
    │  Chatify Route      │
    │  /chatify/...       │
    └──────────┬──────────┘
               ↓
    ┌─────────────────────┐
    │ ChatifyRole         │
    │ Middleware          │ ← First Security Layer
    │ • Checks user auth  │
    │ • Validates target  │
    └──────────┬──────────┘
               ↓
    ┌─────────────────────┐
    │ Messages            │
    │ Controller          │ ← Second Security Layer
    │ • canChatWith()     │
    │ • getAllowed...()   │
    └──────────┬──────────┘
               ↓
    ┌─────────────────────┐
    │ Chatify Core        │
    │ • Process message   │
    │ • Store to DB       │
    └──────────┬──────────┘
               ↓
         ✅ Authorized
         ❌ 403 Forbidden


┌────────────────────────────────────────────────────────────────┐
│                  UMKM-Based Segregation                         │
└────────────────────────────────────────────────────────────────┘

         ┌─────────────┐
         │   UMKM 1    │
         │ "Toko A"    │
         └──────┬──────┘
                │
      ┌─────────┴─────────┐
      │                   │
  ┌───▼───┐          ┌────▼────┐
  │ Admin │          │ User 1  │
  │   A   │◄────────►│ User 2  │
  └───────┘          │ User 3  │
                     └─────────┘
      ✅ Can chat within UMKM

         ┌─────────────┐
         │   UMKM 2    │
         │ "Toko B"    │
         └──────┬──────┘
                │
      ┌─────────┴─────────┐
      │                   │
  ┌───▼───┐          ┌────▼────┐
  │ Admin │          │ User 4  │
  │   B   │◄────────►│ User 5  │
  └───────┘          └─────────┘
      ✅ Can chat within UMKM

  ❌ Admin A cannot chat with User 4
  ❌ User 1 cannot chat with Admin B
  ❌ Admin A cannot chat with Admin B


┌────────────────────────────────────────────────────────────────┐
│               Super Admin Universal Access                      │
└────────────────────────────────────────────────────────────────┘

              ┌─────────────┐
              │ SUPER ADMIN │
              └──────┬──────┘
                     │
       ┌─────────────┼─────────────┐
       │             │             │
       ▼             ▼             ▼
  ┌────────┐    ┌────────┐    ┌────────┐
  │ User 1 │    │Admin A │    │Admin B │
  └────────┘    └────────┘    └────────┘
  ┌────────┐    ┌────────┐    ┌────────┐
  │ User 2 │    │User 3  │    │User 4  │
  └────────┘    └────────┘    └────────┘

  ✅ Super Admin can chat with EVERYONE


┌────────────────────────────────────────────────────────────────┐
│                    Database Schema                              │
└────────────────────────────────────────────────────────────────┘

    ┌──────────────┐         ┌──────────────┐
    │    users     │         │    umkms     │
    ├──────────────┤         ├──────────────┤
    │ id           │         │ id           │
    │ name         │         │ name         │
    │ email        │         │ owner_user_id│◄──┐
    │ role         │         │ status       │   │
    │ umkm_id      │────────►│ ...          │   │
    └──────────────┘         └──────────────┘   │
                                    │            │
                                    │            │
                                    └────────────┘
                                    (owns UMKM)


┌────────────────────────────────────────────────────────────────┐
│                   Contact List Examples                         │
└────────────────────────────────────────────────────────────────┘

┌─────────────────────────┐
│ User 1's Contact List   │
├─────────────────────────┤
│ ✅ Admin A (their admin)│
│ ✅ Super Admin          │
│ ❌ User 2 (other user)  │
│ ❌ Admin B (other admin)│
└─────────────────────────┘

┌─────────────────────────┐
│ Admin A's Contact List  │
├─────────────────────────┤
│ ✅ User 1 (their user)  │
│ ✅ User 2 (their user)  │
│ ✅ User 3 (their user)  │
│ ✅ Super Admin          │
│ ❌ Admin B (other admin)│
│ ❌ User 4 (other's user)│
└─────────────────────────┘

┌─────────────────────────┐
│ Super Admin's Contacts  │
├─────────────────────────┤
│ ✅ ALL Users            │
│ ✅ ALL Admins           │
│ ✅ Other Super Admins   │
│ (Everyone in system)    │
└─────────────────────────┘


┌────────────────────────────────────────────────────────────────┐
│                  Edge Cases Handled                             │
└────────────────────────────────────────────────────────────────┘

Case 1: User without UMKM assignment
┌──────────────────────────┐
│ User (umkm_id = null)    │
├──────────────────────────┤
│ Contact List:            │
│ ✅ Super Admins only     │
│ ❌ No admin_toko visible │
│ ❌ No other users        │
└──────────────────────────┘

Case 2: Admin without UMKM
┌──────────────────────────┐
│ Admin (no UMKM owned)    │
├──────────────────────────┤
│ Contact List:            │
│ ✅ Super Admins only     │
│ ❌ No users visible      │
│ ❌ No other admins       │
└──────────────────────────┘

Case 3: Bidirectional Authorization
┌──────────────────────────┐
│ If A can chat with B,    │
│ then B can chat with A   │
├──────────────────────────┤
│ User 1 ←→ Admin A  ✅    │
│ Admin A ←→ Super Admin ✅│
│ User 1 ←→ User 2  ❌     │
└──────────────────────────┘


┌────────────────────────────────────────────────────────────────┐
│                  Security Layers                                │
└────────────────────────────────────────────────────────────────┘

Layer 1: Authentication
    ↓ Must be logged in
    
Layer 2: ChatifyRoleMiddleware
    ↓ Validates target user ID
    
Layer 3: MessagesController
    ↓ Checks canChatWith()
    
Layer 4: ChatPolicy (optional)
    ↓ Gate-based authorization
    
Layer 5: Database Constraints
    ↓ Foreign keys, constraints
    
    ✅ AUTHORIZED ACTION


┌────────────────────────────────────────────────────────────────┐
│               Testing Coverage Map                              │
└────────────────────────────────────────────────────────────────┘

Super Admin Tests (3)
├─ Can chat with all users ✅
├─ All users can chat with them ✅
└─ Gets all users in contacts ✅

User Tests (5)
├─ Can chat with their admin ✅
├─ Cannot chat with other admin ✅
├─ Cannot chat with other users ✅
├─ Without UMKM can see super admins ✅
└─ Gets correct contact list ✅

Admin Tests (5)
├─ Can chat with their users ✅
├─ Cannot chat with other users ✅
├─ Cannot chat with other admins ✅
├─ Without UMKM can see super admins ✅
└─ Gets correct contact list ✅

General Tests (1)
└─ Authorization is bidirectional ✅

Total: 14 tests, 39 assertions, 100% pass rate


┌────────────────────────────────────────────────────────────────┐
│                    Quick Reference                              │
└────────────────────────────────────────────────────────────────┘

Check if user can chat:
    $currentUser->canChatWith($targetUser)
    
Get allowed contacts:
    $currentUser->getAllowedChatUsers()
    
Assign user to UMKM:
    $user->update(['umkm_id' => $umkmId])
    
Test authorization:
    php artisan test --filter=ChatAuthorizationTest
    
Clear caches:
    php artisan config:clear
    php artisan cache:clear


═══════════════════════════════════════════════════════════════════
  Platform U-LINK - Secure Role-Based Chat System
  Built with Laravel 12 + Chatify + PostgreSQL
═══════════════════════════════════════════════════════════════════
