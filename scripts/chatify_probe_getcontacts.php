<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Chatify\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$controller = app(MessagesController::class);

$userIds = [1, 3, 4, 5, 6, 7, 8];

foreach ($userIds as $id) {
    Auth::logout();
    Auth::loginUsingId($id);

    $req = Request::create('/chatify/getContacts', 'GET', ['page' => 1]);
    $res = $controller->getContacts($req);
    $data = $res->getData(true);

    $contactsHtml = (string) ($data['contacts'] ?? '');
    $total = $data['total'] ?? null;
    $lastPage = $data['last_page'] ?? null;

    echo "user_id={$id} auth=" . (Auth::id() ?? 'NULL') . " total={$total} last_page={$lastPage} contacts_len=" . strlen($contactsHtml) . PHP_EOL;
}
