<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\IntakeController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CountryCityState;
use App\Http\Controllers\Admin\ApplicationHistoryController;
use App\Http\Controllers\Admin\ApplicationStagesController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\VisaToolsController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\URM_UniversityController;
use App\Http\Controllers\Admin\RefundRequestController;
use App\Http\Controllers\Admin\UniversityPresentationController;
use App\Http\Controllers\Admin\SearchCourseController;
use App\Http\Controllers\Admin\EntryRequirementController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\EntryController;
use App\Http\Controllers\Admin\CompanyTransactionController;
use App\Http\Controllers\Admin\UserTransactionController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Application
    Route::get('/application/create', [ApplicationController::class, 'create'])->name('application.create');
    Route::post('/application/detail-fetch', [ApplicationController::class, 'detailFetch'])->name('application.detail-fetch');
    Route::post('/application/detail', [ApplicationController::class, 'detail'])->name('application.detail');
    Route::post('/application/student-info', [ApplicationController::class, 'studentInfo'])->name('application.student-info');
    Route::post('/application/student-document', [ApplicationController::class, 'studentDocument'])->name('application.student-document');
    Route::post('/application/getCourseType', [ApplicationController::class, 'getCourseType'])->name('application.getCourseType');
    Route::post('/application/getUniversityList', [ApplicationController::class, 'getUniversityList'])->name('application.getUniversityList');
    Route::post('/application/getCourse', [ApplicationController::class, 'getCourse'])->name('application.getCourse');
    Route::post('/application/student_info_save', [ApplicationController::class, 'student_info_save'])->name('application.student_info_save');
    Route::post('/application/upload_student_document', [ApplicationController::class, 'upload_student_document'])->name('application.upload_student_document');
    Route::post('/application/application_submit', [ApplicationController::class, 'application_submit'])->name('application.application_submit');

    //Application History Routes
    Route::get('/application-history', [ApplicationHistoryController::class, 'index'])->name('application-history.index');
    Route::get('/application-history/create', [ApplicationHistoryController::class, 'create'])->name('application-history.create');
    Route::get('/application-history', [ApplicationHistoryController::class, 'index'])->name('application-history.index');
    Route::get('/application-history/{id}/edit', [ApplicationHistoryController::class, 'edit'])->name('application-history.edit');
    Route::post('/application-history/{id}/update', [ApplicationHistoryController::class, 'update'])->name('application-history.update');
    Route::post('/application-history/store', [ApplicationHistoryController::class, 'store'])->name('application-history.store');
    Route::post('/application-history/change_status', [ApplicationHistoryController::class, 'change_status'])->name('application-history.change_status');
    Route::get('/application-history/fetch', [ApplicationHistoryController::class, 'fetch'])->name('application-history.fetch');
    Route::delete('/application-history/{id}', [ApplicationHistoryController::class, 'deleteIntakes'])->name('application-history.delete');
    Route::get('/application-history/CountData', [ApplicationHistoryController::class, 'CountData'])->name('application-history.CountData');
    Route::post('/application-history/student-info', [ApplicationHistoryController::class, 'studentInfo'])->name('application-history.student-info');
    Route::post('/application-history/student_info_save', [ApplicationHistoryController::class, 'student_info_save'])->name('application-history.student_info_save');
    Route::post('/application-history/course_detail', [ApplicationHistoryController::class, 'course_detail'])->name('application-history.course_detail');
    Route::post('/application-history/student-document', [ApplicationHistoryController::class, 'studentDocument'])->name('application-history.student-document');
    Route::post('/application-history/application-history', [ApplicationHistoryController::class, 'application_history'])->name('application-history.application-history');
    Route::post('/application-history/comments', [ApplicationHistoryController::class, 'comments'])->name('application-history.comments');
    Route::post('/application-history/comments_save', [ApplicationHistoryController::class, 'comments_save'])->name('application-history.comments_save');
    Route::post('/application-history/fetch_comments', [ApplicationHistoryController::class, 'fetch_comments'])->name('application-history.fetch_comments');
    Route::post('/application-history/urm_detail', [ApplicationHistoryController::class, 'urm_detail'])->name('application-history.urm_detail');
    Route::post('/application-history/assign_application', [ApplicationHistoryController::class, 'assign_application'])->name('application-history.assign_application');
    Route::post('/application-history/assign_application_save', [ApplicationHistoryController::class, 'assign_application_save'])->name('application-history.assign_application_save');
    Route::post('/application-history/update_status_assignee', [ApplicationHistoryController::class, 'update_status_assignee'])->name('application-history.update_status_assignee');

    // Role Routes
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/role/{id}/update', [RoleController::class, 'update'])->name('role.update');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
    Route::post('/role/change_status', [RoleController::class, 'change_status'])->name('role.change_status');
    Route::get('/role/fetch', [RoleController::class, 'fetchRoles'])->name('role.fetch');
    Route::delete('/role/{id}', [RoleController::class, 'deleteRole'])->name('role.delete');
    Route::get('/role/CountData', [RoleController::class, 'CountData'])->name('role.CountData');

    // Permission Routes
    Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');
    Route::post('/permission/change_status', [PermissionController::class, 'change_status'])->name('permission.change_status');
    Route::get('/permission/fetch', [PermissionController::class, 'fetch'])->name('permission.fetch');
    Route::delete('/permission/{id}', [PermissionController::class, 'deleteRole'])->name('permission.delete');
    Route::get('/permission/CountData', [PermissionController::class, 'CountData'])->name('permission.CountData');
    Route::get('/permission/user', [PermissionController::class, 'user'])->name('permission.user');
    Route::post('/permission/get_user_permission', [PermissionController::class, 'get_user_permission'])->name('permission.get_user_permission');
    Route::post('/permission/SaveUserPermission', [PermissionController::class, 'SaveUserPermission'])->name('permission.SaveUserPermission');

    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/change_status', [UserController::class, 'change_status'])->name('user.change_status');
    Route::get('/user/fetch', [UserController::class, 'fetchUsers'])->name('user.fetch');
    Route::delete('/user/{id}', [UserController::class, 'deleteUser'])->name('user.delete');
    Route::get('/user/CountData', [UserController::class, 'CountData'])->name('user.CountData');


    //Intake Routes
    Route::get('/intake/create', [IntakeController::class, 'create'])->name('intake.create');
    Route::get('/intake', [IntakeController::class, 'index'])->name('intake.index');
    Route::get('/intake/{id}/edit', [IntakeController::class, 'edit'])->name('intake.edit');
    Route::post('/intake/{id}/update', [IntakeController::class, 'update'])->name('intake.update');
    Route::post('/intake/store', [IntakeController::class, 'store'])->name('intake.store');
    Route::post('/intake/change_status', [IntakeController::class, 'change_status'])->name('intake.change_status');
    Route::get('/intake/fetch', [IntakeController::class, 'fetchIntakes'])->name('intake.fetch');
    Route::delete('/intake/{id}', [IntakeController::class, 'deleteIntakes'])->name('intake.delete');
    Route::get('/intake/CountData', [IntakeController::class, 'CountData'])->name('intake.CountData');

    // ApplicationStages
    Route::get('/application-stage/create', [ApplicationStagesController::class, 'create'])->name('application-stage.create');
    Route::get('/application-stage', [ApplicationStagesController::class, 'index'])->name('application-stage.index');
    Route::get('/application-stage/{id}/edit', [ApplicationStagesController::class, 'edit'])->name('application-stage.edit');
    Route::post('/application-stage/{id}/update', [ApplicationStagesController::class, 'update'])->name('application-stage.update');
    Route::post('/application-stage/store', [ApplicationStagesController::class, 'store'])->name('application-stage.store');
    Route::post('/application-stage/change_status', [ApplicationStagesController::class, 'change_status'])->name('application-stage.change_status');
    Route::get('/application-stage/fetch', [ApplicationStagesController::class, 'fetch'])->name('application-stage.fetch');
    Route::delete('/application-stage/{id}', [ApplicationStagesController::class, 'delete'])->name('application-stage.delete');
    Route::get('/application-stage/CountData', [ApplicationStagesController::class, 'CountData'])->name('application-stage.CountData');



    // ApplicationStages
    Route::get('/comment/create', [CommentController::class, 'create'])->name('comment.create');
    Route::get('/comment', [CommentController::class, 'index'])->name('comment.index');
    Route::get('/comment/{id}/edit', [CommentController::class, 'edit'])->name('comment.edit');
    Route::post('/comment/{id}/update', [CommentController::class, 'update'])->name('comment.update');
    Route::post('/comment/store', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/comment/change_status', [CommentController::class, 'change_status'])->name('comment.change_status');
    Route::get('/comment/fetch', [CommentController::class, 'fetch'])->name('comment.fetch');
    Route::delete('/comment/{id}', [CommentController::class, 'delete'])->name('comment.delete');
    Route::get('/comment/CountData', [CommentController::class, 'CountData'])->name('comment.CountData');


    // Visa tool
    Route::get('/visatool/create', [VisaToolsController::class, 'create'])->name('visatool.create');
    Route::get('/visatool', [VisaToolsController::class, 'view'])->name('visatool.view');
    Route::get('/visatool/view', [VisaToolsController::class, 'index'])->name('visatool.index');
    Route::get('/visatool/{id}/edit', [VisaToolsController::class, 'edit'])->name('visatool.edit');
    Route::post('/visatool/{id}/update', [VisaToolsController::class, 'update'])->name('visatool.update');
    Route::post('/visatool/store', [VisaToolsController::class, 'store'])->name('visatool.store');
    Route::post('/visatool/change_status', [VisaToolsController::class, 'change_status'])->name('visatool.change_status');
    Route::get('/visatool/fetch', [VisaToolsController::class, 'fetch'])->name('visatool.fetch');
    Route::delete('/visatool/{id}', [VisaToolsController::class, 'delete'])->name('visatool.delete');
    Route::get('/visatool/CountData', [VisaToolsController::class, 'CountData'])->name('visatool.CountData');
    Route::get('/visatool/view_data_fetch', [VisaToolsController::class, 'view_data_fetch'])->name('visatool.view_data_fetch');


    // Visa tool
    Route::get('/refund-request/create', [RefundRequestController::class, 'create'])->name('refund-request.create');
    Route::get('/refund-request', [RefundRequestController::class, 'view'])->name('refund-request.view');
    Route::get('/refund-request/view', [RefundRequestController::class, 'index'])->name('refund-request.index');
    Route::get('/refund-request/{id}/edit', [RefundRequestController::class, 'edit'])->name('refund-request.edit');
    Route::post('/refund-request/{id}/update', [RefundRequestController::class, 'update'])->name('refund-request.update');
    Route::post('/refund-request/store', [RefundRequestController::class, 'store'])->name('refund-request.store');
    Route::post('/refund-request/change_status', [RefundRequestController::class, 'change_status'])->name('refund-request.change_status');
    Route::get('/refund-request/fetch', [RefundRequestController::class, 'fetch'])->name('refund-request.fetch');
    Route::delete('/refund-request/{id}', [RefundRequestController::class, 'delete'])->name('refund-request.delete');
    Route::get('/refund-request/CountData', [RefundRequestController::class, 'CountData'])->name('refund-request.CountData');
    Route::get('/refund-request/view_data_fetch', [RefundRequestController::class, 'view_data_fetch'])->name('refund-request.view_data_fetch');


    // University presentation
    Route::get('/university-presentation/create', [UniversityPresentationController::class, 'create'])->name('university-presentation.create');
    Route::get('/university-presentation', [UniversityPresentationController::class, 'view'])->name('university-presentation.view');
    Route::get('/university-presentation/view', [UniversityPresentationController::class, 'index'])->name('university-presentation.index');
    Route::get('/university-presentation/{id}/edit', [UniversityPresentationController::class, 'edit'])->name('university-presentation.edit');
    Route::post('/university-presentation/{id}/update', [UniversityPresentationController::class, 'update'])->name('university-presentation.update');
    Route::post('/university-presentation/store', [UniversityPresentationController::class, 'store'])->name('university-presentation.store');
    Route::post('/university-presentation/change_status', [UniversityPresentationController::class, 'change_status'])->name('university-presentation.change_status');
    Route::get('/university-presentation/fetch', [UniversityPresentationController::class, 'fetch'])->name('university-presentation.fetch');
    Route::delete('/university-presentation/{id}', [UniversityPresentationController::class, 'delete'])->name('university-presentation.delete');
    Route::get('/university-presentation/CountData', [UniversityPresentationController::class, 'CountData'])->name('university-presentation.CountData');
    Route::get('/university-presentation/view_data_fetch', [UniversityPresentationController::class, 'view_data_fetch'])->name('university-presentation.view_data_fetch');



    //SearchCourses
    Route::get('/search-course/create', [SearchCourseController::class, 'create'])->name('search-course.create');
    Route::get('/search-course', [SearchCourseController::class, 'view'])->name('search-course.view');
    Route::get('/search-course/view', [SearchCourseController::class, 'index'])->name('search-course.index');
    Route::get('/search-course/{id}/edit', [SearchCourseController::class, 'edit'])->name('search-course.edit');
    Route::post('/search-course/{id}/update', [SearchCourseController::class, 'update'])->name('search-course.update');
    Route::post('/search-course/store', [SearchCourseController::class, 'store'])->name('search-course.store');
    Route::post('/search-course/change_status', [SearchCourseController::class, 'change_status'])->name('search-course.change_status');
    Route::get('/search-course/fetch', [SearchCourseController::class, 'fetch'])->name('search-course.fetch');
    Route::delete('/search-course/{id}', [SearchCourseController::class, 'delete'])->name('search-course.delete');
    Route::get('/search-course/CountData', [SearchCourseController::class, 'CountData'])->name('search-course.CountData');
    Route::get('/search-course/view_data_fetch', [SearchCourseController::class, 'view_data_fetch'])->name('search-course.view_data_fetch');
    Route::post('/search-course/get_intake_detail', [SearchCourseController::class, 'get_intake_detail'])->name('search-course.get_intake_detail');



    //Entry Requirements
    Route::get('/entry-requirement/create', [EntryRequirementController::class, 'create'])->name('entry-requirement.create');
    Route::get('/entry-requirement', [EntryRequirementController::class, 'view'])->name('entry-requirement.view');
    Route::get('/entry-requirement', [EntryRequirementController::class, 'index'])->name('entry-requirement.index');
    Route::get('/entry-requirement/{id}/edit', [EntryRequirementController::class, 'edit'])->name('entry-requirement.edit');
    Route::post('/entry-requirement/{id}/update', [EntryRequirementController::class, 'update'])->name('entry-requirement.update');
    Route::post('/entry-requirement/store', [EntryRequirementController::class, 'store'])->name('entry-requirement.store');
    Route::post('/entry-requirement/change_status', [EntryRequirementController::class, 'change_status'])->name('entry-requirement.change_status');
    Route::get('/entry-requirement/fetch', [EntryRequirementController::class, 'fetch'])->name('entry-requirement.fetch');
    Route::delete('/entry-requirement/{id}', [EntryRequirementController::class, 'delete'])->name('entry-requirement.delete');
    Route::get('/entry-requirement/CountData', [EntryRequirementController::class, 'CountData'])->name('entry-requirement.CountData');
    Route::get('/entry-requirement/view_data_fetch', [EntryRequirementController::class, 'view_data_fetch'])->name('entry-requirement.view_data_fetch');


      //Notice Requirements
    Route::get('/notice/create', [NoticeController::class, 'create'])->name('notice.create');
    Route::get('/notice', [NoticeController::class, 'view'])->name('notice.view');
    Route::get('/notice', [NoticeController::class, 'index'])->name('notice.index');
    Route::get('/notice/{id}/edit', [NoticeController::class, 'edit'])->name('notice.edit');
    Route::post('/notice/{id}/update', [NoticeController::class, 'update'])->name('notice.update');
    Route::post('/notice/store', [NoticeController::class, 'store'])->name('notice.store');
    Route::post('/notice/change_status', [NoticeController::class, 'change_status'])->name('notice.change_status');
    Route::get('/notice/fetch', [NoticeController::class, 'fetch'])->name('notice.fetch');
    Route::delete('/notice/{id}', [NoticeController::class, 'delete'])->name('notice.delete');
    Route::get('/notice/CountData', [NoticeController::class, 'CountData'])->name('notice.CountData');
    Route::get('/notice/view_data_fetch', [NoticeController::class, 'view_data_fetch'])->name('notice.view_data_fetch');



    //University Routes
    Route::get('/university/create', [UniversityController::class, 'create'])->name('university.create');
    Route::get('/university', [UniversityController::class, 'index'])->name('university.index');
    Route::get('/university/{id}/edit', [UniversityController::class, 'edit'])->name('university.edit');
    Route::post('/university/{id}/update', [UniversityController::class, 'update'])->name('university.update');
    Route::post('/university/store', [UniversityController::class, 'store'])->name('university.store');
    Route::post('/university/change_status', [UniversityController::class, 'change_status'])->name('university.change_status');
    Route::get('/university/fetch', [UniversityController::class, 'fetch'])->name('university.fetch');
    Route::delete('/university/{id}', [UniversityController::class, 'delete'])->name('university.delete');
    Route::get('/university/CountData', [UniversityController::class, 'CountData'])->name('university.CountData');


     //Intake Routes


    //University Routes
    Route::get('/urm-university', [URM_UniversityController::class, 'index'])->name('urm_university.index');
    Route::post('/university/fetch', [URM_UniversityController::class, 'fetch'])->name('urm_university.fetch');








     //Intake Routes
    Route::get('/course/create', [CourseController::class, 'create'])->name('course.create');
    Route::get('/course', [CourseController::class, 'index'])->name('course.index');
    Route::get('/course/{id}/edit', [CourseController::class, 'edit'])->name('course.edit');
    Route::put('/course/{id}/update', [CourseController::class, 'update'])->name('course.update');
    Route::post('/course/store', [CourseController::class, 'store'])->name('course.store');
    Route::post('/course/change_status', [CourseController::class, 'change_status'])->name('course.change_status');
    Route::get('/course/fetch', [CourseController::class, 'fetch'])->name('course.fetch');
    Route::delete('/course/{id}', [CourseController::class, 'delete'])->name('course.delete');
    Route::get('/course/CountData', [CourseController::class, 'CountData'])->name('course.CountData');

    // CountryStateCity
    Route::post('/get-state', [CountryCityState::class, 'getState'])->name('state.get');

    //Use Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/documents/download/{id}', [DocumentController::class, 'downloadDocument'])->name('documents.download');
    Route::get('/documents/preview/{id}', [DocumentController::class, 'previewDocument'])->name('documents.preview');
    Route::post('/upload-image', [DocumentController::class, 'uploadImage'])->name('documents.uploadImage');




    // routes/web.php







    //ClientController
    Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');
    Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    Route::get('/client/{id}/edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::get('/client/{id}/view', [ClientController::class, 'view'])->name('client.view');
    Route::post('/client/{id}/update', [ClientController::class, 'update'])->name('client.update');
    Route::post('/client/store', [ClientController::class, 'store'])->name('client.store');
    Route::post('/client/change_status', [ClientController::class, 'change_status'])->name('client.change_status');
    Route::get('/client/fetch', [ClientController::class, 'fetch'])->name('client.fetch');
    Route::delete('/client/{id}', [ClientController::class, 'deleteIntakes'])->name('client.delete');
    Route::get('/client/CountData', [ClientController::class, 'CountData'])->name('client.CountData');


     //CompanyController
     Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
     Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
     Route::get('/company/{id}/edit', [CompanyController::class, 'edit'])->name('company.edit');
     Route::post('/company/{id}/update', [CompanyController::class, 'update'])->name('company.update');
     Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
     Route::post('/company/change_status', [CompanyController::class, 'change_status'])->name('company.change_status');
     Route::get('/company/fetch', [CompanyController::class, 'fetch'])->name('company.fetch');
     Route::delete('/company/{id}', [CompanyController::class, 'deleteIntakes'])->name('company.delete');
     Route::get('/company/CountData', [CompanyController::class, 'CountData'])->name('company.CountData');


    //CompanyTransactionController
    Route::get('/company-transaction/create', [CompanyTransactionController::class, 'create'])->name('company_transaction.create');
    Route::get('/company-transaction', [CompanyTransactionController::class, 'index'])->name('company_transaction.index');
    Route::get('/company-transaction/{id}/edit', [CompanyTransactionController::class, 'edit'])->name('company_transaction.edit');
    Route::post('/company-transaction/{id}/update', [CompanyTransactionController::class, 'update'])->name('company_transaction.update');
    Route::post('/company-transaction/store', [CompanyTransactionController::class, 'store'])->name('company_transaction.store');
    Route::post('/company-transaction/change_status', [CompanyTransactionController::class, 'change_status'])->name('company_transaction.change_status');
    Route::get('/company-transaction/fetch', [CompanyTransactionController::class, 'fetch'])->name('company_transaction.fetch');
    Route::delete('/company-transaction/{id}', [CompanyTransactionController::class, 'deleteIntakes'])->name('company_transaction.delete');
    Route::get('/company-transaction/CountData', [CompanyTransactionController::class, 'CountData'])->name('company_transaction.CountData');


    //UserTransactionController
    Route::get('/user-transaction/create', [UserTransactionController::class, 'create'])->name('user_transaction.create');
    Route::get('/user-transaction', [UserTransactionController::class, 'index'])->name('user_transaction.index');
    Route::get('/user-transaction/{id}/edit', [UserTransactionController::class, 'edit'])->name('user_transaction.edit');
    Route::post('/user-transaction/{id}/update', [UserTransactionController::class, 'update'])->name('user_transaction.update');
    Route::post('/user-transaction/store', [UserTransactionController::class, 'store'])->name('user_transaction.store');
    Route::post('/user-transaction/change_status', [UserTransactionController::class, 'change_status'])->name('user_transaction.change_status');
    Route::get('/user-transaction/fetch', [UserTransactionController::class, 'fetch'])->name('user_transaction.fetch');
    Route::delete('/user-transaction/{id}', [UserTransactionController::class, 'deleteIntakes'])->name('user_transaction.delete');
    Route::get('/user-transaction/CountData', [UserTransactionController::class, 'CountData'])->name('user_transaction.CountData');

     //vendorsController
     Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendor.create');
     Route::get('/vendors', [VendorController::class, 'index'])->name('vendor.index');
     Route::get('/vendors/{id}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
     Route::post('/vendors/{id}/update', [VendorController::class, 'update'])->name('vendor.update');
     Route::post('/vendors/store', [VendorController::class, 'store'])->name('vendor.store');
     Route::post('/vendors/change_status', [VendorController::class, 'change_status'])->name('vendor.change_status');
     Route::get('/vendors/fetch', [VendorController::class, 'fetch'])->name('vendor.fetch');
     Route::delete('/vendors/{id}', [VendorController::class, 'deleteIntakes'])->name('vendor.delete');
     Route::get('/vendors/CountData', [VendorController::class, 'CountData'])->name('vendor.CountData');


     //ProductController
     Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
     Route::get('/product', [ProductController::class, 'index'])->name('product.index');
     Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
     Route::post('/product/{id}/update', [ProductController::class, 'update'])->name('product.update');
     Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
     Route::post('/product/change_status', [ProductController::class, 'change_status'])->name('product.change_status');
     Route::get('/product/fetch', [ProductController::class, 'fetch'])->name('product.fetch');
     Route::delete('/product/{id}', [ProductController::class, 'deleteIntakes'])->name('product.delete');
     Route::get('/product/CountData', [ProductController::class, 'CountData'])->name('product.CountData');


     //EntryController
     Route::get('/entry/create', [EntryController::class, 'create'])->name('entry.create');
     Route::get('/entry', [EntryController::class, 'index'])->name('entry.index');
     Route::post('/entry/get_list_vendor_client', [EntryController::class, 'get_list_vendor_client'])->name('entry.get_list_vendor_client');
     Route::post('/entry/get_detail', [EntryController::class, 'get_detail'])->name('entry.get_detail');
     Route::post('/entry/get_transaction_page', [EntryController::class, 'get_transaction_page'])->name('entry.get_transaction_page');
     Route::post('/entry/save-transaction', [EntryController::class, 'saveTransaction'])->name('entry.save_transaction');
     Route::post('/entry/product_detail', [EntryController::class, 'product_detail'])->name('entry.product_detail');

     Route::get('/entry/{id}/edit', [EntryController::class, 'edit'])->name('entry.edit');
     Route::get('/entry/{id}/view', [EntryController::class, 'view'])->name('entry.view');
     Route::post('/entry/{id}/update', [EntryController::class, 'update'])->name('entry.update');
     Route::post('/entry/store', [EntryController::class, 'store'])->name('entry.store');
     Route::post('/entry/change_status', [EntryController::class, 'change_status'])->name('entry.change_status');
     Route::get('/entry/fetch', [EntryController::class, 'fetch'])->name('entry.fetch');
     Route::delete('/entry/{id}', [EntryController::class, 'deleteIntakes'])->name('entry.delete');
     Route::get('/entry/CountData', [EntryController::class, 'CountData'])->name('entry.CountData');
     Route::get('/entry/{id}/download-bill', [EntryController::class, 'downloadBillPDF'])->name('download.bill');



});

require __DIR__.'/auth.php';


