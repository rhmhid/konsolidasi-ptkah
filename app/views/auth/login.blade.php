
<!DOCTYPE html>
<html lang="id">
	<!--begin::Head-->
	<head>
		<base href="" />
		<title>.:: Login | {{ dataConfigs('app_name_long') }} ::.</title>

		<meta charset="utf-8" />
		<meta name="description" content="{{ dataConfigs('app_name') }} - Based Integrated Application Management Solution" />
        <meta name="keywords" content="{{ dataConfigs('app_name_long') }}" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<link rel="shortcut icon" href="{{ asset(dataConfigs('app_icon')) }}" />

		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->

		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->

	<!--begin::Body-->
	<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center" style="background-image: url('{{ asset('assets/media/auth/bg10.jpeg') }}');">
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Aside-->
				<div class="d-flex flex-lg-row-fluid">
					<!--begin::Content-->
					<div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
						<!--begin::Image-->
						<img class="mx-auto mw-100 mb-1 mb-lg-2" src="{{ dataConfigs('company_logo_login') }}" alt="" />
						<!--end::Image-->

						<!--begin::Text-->
						<div class="text-gray-600 fs-base text-center fw-semibold d-none">
							In this kind of post, 
							<a href="javascript:void(0)" class="opacity-75-hover text-primary me-1">the blogger</a>introduces a person they’ve interviewed 
							<br />and provides some background information about 
							<a href="javascript:void(0)" class="opacity-75-hover text-primary me-1">the interviewee</a>and their 
							<br />work following this is a transcript of the interview.
						</div>
						<!--end::Text-->
					</div>
					<!--end::Content-->
				</div>
				<!--begin::Aside-->

				<!--begin::Body-->
				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
					<!--begin::Wrapper-->
					<div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
						<!--begin::Content-->
						<div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
							<!--begin::Wrapper-->
							<div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
								<!--begin::Form-->
								<form class="form w-100" novalidate="novalidate" id="login-form" method="POST" action="{{ route('login') }}">
									<input type="hidden" name="intended" value="{{ $intended }}" />

									<!--begin::Heading-->
									<div class="mb-11">
										<!--begin::Title-->
										<h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
										<!--end::Title-->

										<!--begin::Subtitle-->
										<div class="text-gray-500 fw-semibold fs-6 d-none">Hello, welcome back, before continue, please entry your account credentials detail.</div>
										<!--end::Subtitle=-->
									</div>
									<!--begin::Heading-->

									<!--begin::Input group=-->
									<div class="fv-row mb-8">
										<!--begin::Label-->
		                                <label class="form-label fs-6 fw-bolder text-dark">Username</label>
		                                <!--end::Label-->

										<!--begin::Username-->
										<input type="text" name="username" value="{{ session('_auth_username') }}" autocomplete="off" class="form-control form-control-lg" />
										<!--end::Username-->
									</div>
									<!--end::Input group=-->

									<div class="fv-row mb-10" data-kt-password-meter="true">
										<!--begin::Wrapper-->
		                                <div class="d-flex flex-stack mb-2">
		                                    <!--begin::Label-->
		                                    <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
		                                    <!--end::Label-->
		                                </div>
		                                <!--end::Wrapper-->

		                                <!--begin::Input-->
	                                	<div class="position-relative">
											<!--begin::Password-->
											<input type="password" name="password" autocomplete="off" class="form-control form-control-lg" />
											<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
		                                        <i class="bi bi-eye-slash fs-2"></i>
		                                        <i class="bi bi-eye fs-2 d-none"></i>
		                                    </span>
											<!--end::Password-->
										</div>
		                                <!--end::Input-->

		                                <!--begin::Meter-->
		                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">

		                                </div>
		                                <!--end::Meter-->
									</div>
									<!--end::Input group=-->

									@if (!empty($messages))
		            				<!--begin::Alert-->
		            				<div class="alert bg-danger d-flex flex-column flex-sm-row p-5 mb-10">
		            					<span class="las la-exclamation-triangle text-white fs-2 me-4 mb-5 mb-sm-0"></span>
		            					<div class="d-flex flex-column text-light pe-0 pe-sm-10">
		            						<h5 class="mb-1 text-white">Error</h5>
		            						<span>{{ config($messages['danger']) }}</span>
		            					</div>
		            				</div>
		            				<!--end::Alert-->
		            				@endif

									<!--begin::Submit button-->
									<div class="d-grid mb-10">
										<button type="submit" id="login-btn" class="btn btn-dark">
											<!--begin::Indicator label-->
											<span class="indicator-label">Login</span>
											<!--end::Indicator label-->

											<!--begin::Indicator progress-->
											<span class="indicator-progress">
												Please wait... 
												<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
											</span>
											<!--end::Indicator progress-->
										</button>
									</div>
									<!--end::Submit button-->
								</form>
								<!--end::Form-->
							</div>
							<!--end::Wrapper-->

							<!--begin::Footer-->
							<div class="d-flex flex-center flex-column-auto">
								<!--begin::Links-->
								<div class="d-flex flex-center fw-bold fs-8">
		                            Copyright © 2024 <a href="https://#" class="text-muted px-2" target="_blank">Estusae Studio</a>
		                        </div>
								<!--end::Links-->
							</div>
							<!--end::Footer-->
						</div>
						<!--end::Content-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->

		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>

		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<!--end::Global Javascript Bundle-->

		<!--begin::Custom Javascript(used for this page only)-->
		<script type="text/javascript">
			const form = document.getElementById('login-form')

			var validator = FormValidation.formValidation(
			    form,
			    {
			        fields: {
			            'username': {
			                validators: {
			                    notEmpty: {
			                        message: "Username Belum Diisi"
			                    }
			                }
			            },
			            'password': {
			                validators: {
			                    notEmpty: {
			                        message: "Password Belum Diisi"
			                    }
			                }
			            },
			        },

			        plugins: {
			            trigger: new FormValidation.plugins.Trigger(),
			            bootstrap: new FormValidation.plugins.Bootstrap5({
			                rowSelector: '.fv-row',
			                eleInvalidClass: '',
			                eleValidClass: ''
			            })
			        }
			    }
			)

			const submitButton = document.getElementById('login-btn')
			submitButton.addEventListener('click', function (e)
			{
			    e.preventDefault()

			    if (validator)
			    {
			        validator.validate().then(function (status)
			        {
			            if (status == 'Valid')
			            {
			                submitButton.setAttribute('data-kt-indicator', 'on')

			                submitButton.disabled = true

			                setTimeout(function ()
			                {
			                    submitButton.removeAttribute('data-kt-indicator')

			                    submitButton.disabled = false

			                    form.submit()
			                }, 2e3)
			            }
			        })
			    }
			})
		</script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>
