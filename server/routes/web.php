<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Admin'], function()
{
    Route::get('/', 'AdminController@index')->name('index');
    Route::get('admin/isLogin', 'AdminController@isLogin');
    Route::get('admin/getAuth', 'AdminController@getAuth');
    Route::post('admin/login', 'AdminController@login');
    Route::get('admin/logout', 'AdminController@logout');
    Route::get('captach','AdminController@captach');
    
});

Route::group(['namespace' => 'MetlConf', 'middleware' => 'admin.auth'], function()
{
    Route::resource('logApp','LogAppController');
    Route::resource('logType','LogTypeController');
    Route::resource('logTable','LogTableController');
});

Route::group(['namespace' => 'Report', 'middleware' => 'admin.auth'], function()
{
    Route::get('/report/get', 'ReportController@get');
    Route::post('/report/post', 'ReportController@post');
});

Route::group(['namespace' => 'Feedback', 'middleware' => 'admin.auth'], function()
{
    Route::get('/feedback/get', 'FeedbackController@get');
});

Route::group(['namespace' => 'Topic', 'middleware' => 'admin.auth'], function()
{
    Route::get('/topic/get', 'TopicController@get');
    Route::post('/topic/update', 'TopicController@update');
    Route::get('/topic/create', 'TopicController@create');
    Route::post('/topic/store', 'TopicController@store');
    Route::post('/topic/upload', 'TopicController@upload');
});

Route::group(['namespace' => 'Library', 'middleware' => 'admin.auth'], function()
{
    Route::get('/library/get', 'LibraryController@get');
    Route::post('/library/update', 'LibraryController@update');
    Route::get('/library/create', 'LibraryController@create');
    Route::post('/library/store', 'LibraryController@store');
    Route::post('/library/upload', 'LibraryController@upload');

    // Route::get('/library/import', 'LibraryController@import');

});

Route::group(['namespace' => 'TopicCategory', 'middleware' => 'admin.auth'], function()
{
    Route::get('/topicCategory/get', 'TopicCategoryController@get');
    Route::post('/topicCategory/update', 'TopicCategoryController@update');
    Route::get('/topicCategory/create', 'TopicCategoryController@create');
    Route::post('/topicCategory/store', 'TopicCategoryController@store');
    Route::post('/topicCategory/upload', 'TopicCategoryController@upload');
});


Route::group(['namespace' => 'Recommend', 'middleware' => 'admin.auth'], function()
{
    Route::get('/recommend/get', 'RecommendController@get');
    Route::post('/recommend/update', 'RecommendController@update');
    Route::get('/recommend/create', 'RecommendController@create');
    Route::post('/recommend/store', 'RecommendController@store');
});

Route::group(['namespace' => 'QuestionReport', 'middleware' => 'admin.auth'], function()
{
    Route::get('/questionReport/get', 'QuestionReportController@get');
    Route::post('/questionReport/update', 'QuestionReportController@update');
    Route::get('/questionReport/create', 'QuestionReportController@create');
    Route::post('/questionReport/store', 'QuestionReportController@store');
});

Route::group(['namespace' => 'Review', 'middleware' => 'admin.auth'], function()
{
    Route::get('/review/get', 'ReviewController@get');
    Route::post('/review/update', 'ReviewController@update');
    // Route::get('/questionReport/create', 'QuestionReportController@create');
    // Route::post('/questionReport/store', 'QuestionReportController@store');
});
