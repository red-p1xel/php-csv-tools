<?php

use Http\Request;
use Http\Router;
use Http\Session;
use Storage\UploadProcessor;
use Http\Response;
use View\TableBuilder\Body;
use View\TableBuilder\Caption;
use View\TableBuilder\Cell;
use View\TableBuilder\Head;
use View\TableBuilder\HeadCell;
use View\TableBuilder\Row;
use View\TableBuilder\Table;
use App\DataProcessor;

$request = new Request();
$session = new Session();
$router = new Router($request);

$pageTitle = 'title';

$router->get('/', function () use ($session) {
//    $testRegionCode = DataProcessor::testGetRegion('995376870861');
//    print(__DIR__.$session::get('storage_path'));
    print_r($session::all());
    $csvFilePath = __DIR__.$session::get('storage_path');

    if (!empty($session::get('storage_path'))) {
        $dp = DataProcessor::handle(__DIR__.$session::get('storage_path'));
    }

    $session::deleteKeys(['message']);

    $content = <<<HTML
        <h1>Upload File Form</h1>
        <div class="form-wrapper">
            <form class="upload-file" action="/upload" method="post" enctype="multipart/form-data">
                <p>Attach CSV file</p>
                <input type="file" name="file" />
                <button type="submit">Submit</button>
            </form>
        </div>
HTML;

    return Response::view(['title' => 'Index', 'content' => $content]);
});


$router->get('/result', function (Request $request) use ($session) {
    // TODO: Implement render HTML table from CSV file.
    // TODO: Rendered CSV file must containing the statistic data like a tech. requirements
    print_r($session::all());
    print_r($request);
});

$router->post('/upload', function (Request $request) use ($router, $session) {
    $fileUploader = new UploadProcessor($request);
    $result = $fileUploader->handle(__STORAGE__);

    if (is_array($result) && in_array('success', $result)) {
        //TODO: Call method `setMessageBag()` after implementation
        $session->set('message', 'File successfully uploaded');
        $session->set(
            'storage_path',
            mb_stristr($result['data']['storage_path'], __UPLOADS__, false)
        );
        Router::redirect('/result');
    }

    return $result;
});
