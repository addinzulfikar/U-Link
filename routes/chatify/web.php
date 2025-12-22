<?php
/**
 * -----------------------------------------------------------------
 * NOTE : There is two routes has a name (user & group),
 * any change in these two route's name may cause an issue
 * if not modified in all places that used in (e.g Chatify class,
 * Controllers, chatify javascript file...).
 * -----------------------------------------------------------------
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chatify\MessagesController;

/*
* This is the main app route [Chatify Messenger]
*/
Route::get('/', [MessagesController::class, 'index'])->name(config('chatify.routes.prefix'));

/**
 *  Fetch info for specific id [user/group]
 */
Route::post('/idInfo', [MessagesController::class, 'idFetchData']);

/**
 * Send message route
 */
Route::post('/sendMessage', [MessagesController::class, 'send'])->name('send.message');

/**
 * Fetch messages
 */
Route::post('/fetchMessages', [MessagesController::class, 'fetch'])->name('fetch.messages');

/**
 * Download attachments route to create a downloadable links
 */
Route::get('/download/{fileName}', [MessagesController::class, 'download'])->name(config('chatify.attachments.download_route_name'));

/**
 * Authentication for pusher private channels
 */
Route::post('/chat/auth', [MessagesController::class, 'pusherAuth'])->name('pusher.auth');

/**
 * Make messages as seen
 */
Route::post('/makeSeen', [MessagesController::class, 'seen'])->name('messages.seen');

/**
 * Get contacts
 */
Route::get('/getContacts', [MessagesController::class, 'getContacts'])->name('contacts.get');

/**
 * Update contact item data
 */
Route::post('/updateContacts', [MessagesController::class, 'updateContactItem'])->name('contacts.update');


/**
 * Star in favorite list
 */
Route::post('/star', [MessagesController::class, 'favorite'])->name('star');

/**
 * get favorites list
 */
Route::post('/favorites', [MessagesController::class, 'getFavorites'])->name('favorites');

/**
 * Search in messenger
 */
Route::get('/search', [MessagesController::class, 'search'])->name('search');

/**
 * Get shared photos
 */
Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('shared');

/**
 * Delete Conversation
 */
Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation'])->name('conversation.delete');

/**
 * Delete Message
 */
Route::post('/deleteMessage', [MessagesController::class, 'deleteMessage'])->name('message.delete');

/**
 * Update setting
 */
Route::post('/updateSettings', [MessagesController::class, 'updateSettings'])->name('avatar.update');

/**
 * Set active status
 */
Route::post('/setActiveStatus', [MessagesController::class, 'setActiveStatus'])->name('activeStatus.set');






/*
* [Group] view by id
*/
Route::get('/group/{id}', [MessagesController::class, 'index'])->name('group');

/*
* user view by id.
* Note : If you added routes after the [User] which is the below one,
* it will considered as user id.
*
* e.g. - The commented routes below :
*/
// Route::get('/route', function(){ return 'Munaf'; }); // works as a route
Route::get('/{id}', [MessagesController::class, 'index'])->name('user');
// Route::get('/route', function(){ return 'Munaf'; }); // works as a user id
