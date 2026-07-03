<?php
/**
 * 拡張機能を有効化
 */
require_once __DIR__ . '/controllers/SearchController.php';
require_once __DIR__ . '/controllers/GitController.php';
require_once __DIR__ . '/controllers/DebugController.php';
require_once __DIR__ . '/controllers/LSPController.php';

// index.php のルーター設定に以上を追加
// $router->post('/search/find', 'SearchController@search');
// $router->post('/search/replace', 'SearchController@replace');
// $router->post('/git/init', 'GitController@init');
// $router->get('/git/status', 'GitController@status');
// $router->post('/git/commit', 'GitController@commit');
// $router->get('/git/log', 'GitController@log');
// $router->post('/debug/breakpoint', 'DebugController@setBreakpoint');
// $router->delete('/debug/breakpoint', 'DebugController@removeBreakpoint');
// $router->post('/debug/step', 'DebugController@step');
// $router->post('/debug/resume', 'DebugController@resume');
// $router->post('/debug/pause', 'DebugController@pause');
// $router->get('/debug/variables', 'DebugController@getVariables');
// $router->post('/lsp/initialize', 'LSPController@initialize');
// $router->post('/lsp/hover', 'LSPController@hover');
// $router->post('/lsp/completion', 'LSPController@completion');
// $router->post('/lsp/definition', 'LSPController@definition');
// $router->post('/lsp/documentSymbols', 'LSPController@documentSymbols');
