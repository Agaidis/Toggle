<?php

<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Route;

>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
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

Route::get('/', function () {
<<<<<<< HEAD
    return view('home');
});


Route::get('/home',                             'HomeController@index')->name('home');

Route::get('/wellbore/{userId?}',                  'WelboreController@index')->middleware('auth');

Route::get('/user-mmp',                         'UserMMPController@index')->middleware('auth');

Route::get('/justus-mmp',                       'UserMMPController@justus')->middleware('auth');

/*                   ADMIN AREA                  */
Route::get('/admin',                            'AdminController@index')->middleware('auth');

Route::get('/admin/updatePermits',              'AdminController@updatePermits')->middleware('auth');

/*                   LEASE CREATOR                 */
Route::get('/lease-creator',                            'LeaseCreatorController@index')->middleware('auth');

Route::post('/lease-creator',              'LeaseCreatorController@createLease')->middleware('auth')->name('createLease');

/*                  PERMIT STORAGE               */
Route::get('/permit-storage',                   'PermitStorageController@index')->middleware('auth');

Route::get('/permit-storage/sendBack',          'PermitStorageController@sendBack')->middleware('auth');

/*                  MMP PAGE                     */
Route::get('/mm-platform',                      'MMPController@index')->middleware('auth');

Route::get('/new-permits/getNotes',             'MMPController@getNotes')->middleware('auth');

Route::put('/new-permits/updateNotes',          'MMPController@updateNotes')->middleware('auth');

Route::post('/new-permits/updateStatus',        'MMPController@updateStatus')->middleware('auth');

Route::put('/new-permits/updateAssignee',       'MMPController@updateAssignee')->middleware('auth');

Route::put('/new-permits/stitchLeaseToPermit',  'MMPController@stitchLeaseToPermit')->middleware('auth');

Route::get('/new-permits/getPermitDetails',     'MMPController@getPermitDetails')->middleware('auth');

Route::post('/new-permits/delete/delete-note',  'MMPController@deleteNote')->middleware('auth');

Route::get('/new-permits/storePermit',          'MMPController@storePermit')->middleware('auth');
=======
    return view('welcome');
});

Auth::routes();

Route::middleware(['Auth:sanctum', 'verified'])->get('/mm-platform', 'MMPController@index')->middleware('Auth')->name('mm-platform');

Route::get('/home',                             'HomeController@index')->name('home');

Route::get('/wellbore/{userId?}',                  'WelboreController@index')->middleware('Auth');

Route::get('/user-mmp',                         'UserMMPController@index')->middleware('Auth');

Route::get('/justus-mmp',                       'UserMMPController@justus')->middleware('Auth');

/*                   ADMIN AREA                  */
Route::get('/admin',                            'AdminController@index')->middleware('Auth')->name('admin');

Route::get('/admin/updatePermits',              'AdminController@updatePermits')->middleware('Auth');

/*                   LEASE CREATOR                 */
Route::get('/lease-creator',                            'LeaseCreatorController@index')->middleware('Auth')->name('lease-creator');

Route::post('/lease-creator',              'LeaseCreatorController@createLease')->middleware('Auth')->name('createLease');

/*                  PERMIT STORAGE               */
Route::get('/permit-storage',                   'PermitStorageController@index')->middleware('Auth')->name('permit-storage');

Route::get('/permit-storage/sendBack',          'PermitStorageController@sendBack')->middleware('Auth');

/*                  MMP PAGE                     */
Route::get('/mm-platform',                      'MMPController@index')->middleware('Auth')->name('mm-platform');

Route::get('/new-permits/getNotes',             'MMPController@getNotes')->middleware('Auth');

Route::put('/new-permits/updateNotes',          'MMPController@updateNotes')->middleware('Auth');

Route::post('/new-permits/updateStatus',        'MMPController@updateStatus')->middleware('Auth');

Route::put('/new-permits/updateAssignee',       'MMPController@updateAssignee')->middleware('Auth');

Route::put('/new-permits/stitchLeaseToPermit',  'MMPController@stitchLeaseToPermit')->middleware('Auth');

Route::get('/new-permits/getPermitDetails',     'MMPController@getPermitDetails')->middleware('Auth');

Route::post('/new-permits/delete/delete-note',  'MMPController@deleteNote')->middleware('Auth');

Route::get('/new-permits/storePermit',          'MMPController@storePermit')->middleware('Auth');
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a

Route::put('/update-prices',                    'MMPController@updatePrices');

/*                                  LEASE/WELLBORE PAGE                        */
<<<<<<< HEAD
Route::get('/lease-page/{interestArea?}/{leaseName}/{isProducing?}/{permitId?}', 'LeasePageController@index')->middleware('auth');

Route::post('/lease-page/updateAcreage',        'LeasePageController@updateAcreage')->middleware('auth');

Route::post('/lease-page/updateLeaseNames',     'LeasePageController@updateLeaseNames')->middleware('auth');

Route::post('/lease-page/updateWellNames',      'LeasePageController@updateWellNames')->middleware('auth');

Route::put('/lease-page/updateAssignee',        'LeasePageController@updateAssignee')->middleware('auth');

Route::put('/lease-page/updateWellType',        'LeasePageController@updateWellType')->middleware('auth');

Route::put('/lease-page/updateFollowUp',        'LeasePageController@updateFollowUp')->middleware('auth');

Route::get('/lease-page/getOwnerInfo',          'LeasePageController@getOwnerInfo')->middleware('auth');

Route::post('/lease-page/update/OwnerPrice',    'LeasePageController@updateOwnerPrice')->middleware('auth');

Route::get('/lease-page/getWellDetails',        'LeasePageController@getWellInfo')->middleware('auth');

Route::get('/lease-page/getNotes',              'LeasePageController@getNotes')->middleware('auth');

Route::put('/lease-page/updateNotes',           'LeasePageController@updateNotes')->middleware('auth');

Route::post('/lease-page/delete-note',          'LeasePageController@deleteNote')->middleware('auth');

Route::get('/lease-page/getOwnerNumbers',       'LeasePageController@getOwnerNumbers')->middleware('auth');

Route::post('/lease-page/addPhone',             'LeasePageController@addPhone')->middleware('auth');

Route::put('/lease-page/pushPhoneNumber',       'LeasePageController@pushPhoneNumber')->middleware('auth');

Route::post('/lease-page/softDeletePhone',      'LeasePageController@softDeletePhone')->middleware('auth');

/*                  PUSHED PHONE NUMBER PAGE            */
Route::get('/pushed-phone-numbers',                     'PushedPhoneNumbersController@index')->middleware('auth');

Route::put('/pushed-phone-numbers/updatePhoneNumber',   'PushedPhoneNumbersController@updatePhoneNumber')->middleware('auth');

Route::post('/pushed-phone-numbers/insertPhoneNumber',  'PushedPhoneNumbersController@insertPhoneNumber')->middleware('auth');

/*                      OWNER PAGE                     */
Route::get('/owner/{interestArea?}/{ownerName?}/{isProducing?}',       'OwnersController@index')->middleware('auth');

Route::put('/owner/updateEmail',                        'OwnersController@updateEmail')->middleware('auth');
=======
Route::get('/lease-page/{interestArea?}/{leaseName}/{isProducing?}/{permitId?}', 'LeasePageController@index')->middleware('Auth');

Route::post('/lease-page/updateAcreage',        'LeasePageController@updateAcreage')->middleware('Auth');

Route::post('/lease-page/updateLeaseNames',     'LeasePageController@updateLeaseNames')->middleware('Auth');

Route::post('/lease-page/updateWellNames',      'LeasePageController@updateWellNames')->middleware('Auth');

Route::put('/lease-page/updateAssignee',        'LeasePageController@updateAssignee')->middleware('Auth');

Route::put('/lease-page/updateWellType',        'LeasePageController@updateWellType')->middleware('Auth');

Route::put('/lease-page/updateFollowUp',        'LeasePageController@updateFollowUp')->middleware('Auth');

Route::get('/lease-page/getOwnerInfo',          'LeasePageController@getOwnerInfo')->middleware('Auth');

Route::post('/lease-page/update/OwnerPrice',    'LeasePageController@updateOwnerPrice')->middleware('Auth');

Route::get('/lease-page/getWellDetails',        'LeasePageController@getWellInfo')->middleware('Auth');

Route::get('/lease-page/getNotes',              'LeasePageController@getNotes')->middleware('Auth');

Route::put('/lease-page/updateNotes',           'LeasePageController@updateNotes')->middleware('Auth');

Route::post('/lease-page/delete-note',          'LeasePageController@deleteNote')->middleware('Auth');

Route::get('/lease-page/getOwnerNumbers',       'LeasePageController@getOwnerNumbers')->middleware('Auth');

Route::post('/lease-page/addPhone',             'LeasePageController@addPhone')->middleware('Auth');

Route::put('/lease-page/pushPhoneNumber',       'LeasePageController@pushPhoneNumber')->middleware('Auth');

Route::post('/lease-page/softDeletePhone',      'LeasePageController@softDeletePhone')->middleware('Auth');

/*                  PUSHED PHONE NUMBER PAGE            */
Route::get('/pushed-phone-numbers',                     'PushedPhoneNumbersController@index')->middleware('Auth')->name('pushed-phone-numbers');

Route::put('/pushed-phone-numbers/updatePhoneNumber',   'PushedPhoneNumbersController@updatePhoneNumber')->middleware('Auth');

Route::post('/pushed-phone-numbers/insertPhoneNumber',  'PushedPhoneNumbersController@insertPhoneNumber')->middleware('Auth');

/*                      OWNER PAGE                     */
Route::get('/owner/{interestArea?}/{ownerName?}/{isProducing?}',       'OwnersController@index')->middleware('Auth');

Route::put('/owner/updateEmail',                        'OwnersController@updateEmail')->middleware('Auth');
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
