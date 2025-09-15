<!DOCTYPE html>
<html lang="id">
	<!--begin::Head-->
	<head>
        <base href="" />
		<title>{{ dataConfigs('app_name') }} - Based Integrated Application Management Solution</title>

		<meta charset="utf-8" />
		<meta name="description" content="{{ dataConfigs('app_name') }} - Based Integrated Application Management Solution" />
        <meta name="keywords" content="{{ dataConfigs('app_name_long') }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

		<link rel="shortcut icon" href="{{ asset(dataConfigs('app_icon')) }}" />

		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->

		<!--begin::Vendor Stylesheets(used for this page only)-->
		<link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Vendor Stylesheets-->

		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->

		<!--begin::Page Custom Stylesheets(used by this page)-->
        <link href="{{ asset('assets/css/customs.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Page Custom Stylesheets(used by this page)-->

        @stack('css')
	</head>
	<!--end::Head-->

	<!--begin::Body-->
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on" data-kt-app-layout="light-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
		<!--begin::loader-->
		<div class="app-page-loader flex-column">
			<span class="spinner-border text-dark" role="status"></span>
			<span class="text-muted fs-6 fw-semibold mt-5">Memuat...</span>
		</div>
		<!--end::Loader-->

		<!--begin::App-->
		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<!--begin::Page-->
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				<!--begin::Header-->
				@include('layouts.main.header')
				<!--end::Header-->

				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					<!--begin::Sidebar-->
					@include('layouts.main.sidebar')
					<!--end::Sidebar-->

					<!--begin::Main-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid">
							<!--begin::Content-->
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<!--begin::Content container-->
								<div id="kt_app_content_container" class="app-container container-fluid mt-4" style="padding-right: 1rem !important; padding-left: 1rem !important;">
									@yield('content')
								</div>
								<!--end::Content container-->
							</div>
							<!--end::Content-->
						</div>
						<!--end::Content wrapper-->

						<!--begin::Footer-->
						@include('layouts.main.footer')
						<!--end::Footer-->
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::App-->

		<!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop bg-dark" data-kt-scrolltop="true">
			<i class="ki-outline ki-arrow-up"></i>
		</div>
        <!--end::Scrolltop-->

        <form id="form-logout" action="{{ route('logout') }}" method="POST">

            <input type="hidden" name="intended">
        </form>

        <div id="konfigurasi-akun-modal" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false" class="modal fade">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content"></div>
            </div>
        </div>

        <div class="modal fade" id="popup_auth_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup_auth_user" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered mw-550px">
		        <div class="modal-content"></div>
		    </div>
		</div>

		@php
            $_isMultiTenants = isMultiTenants();
        @endphp

        @if ($_isMultiTenants == 't')
		<div class="modal fade" id="popup-branch" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="popup-branch" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered">
		        <div class="modal-content"></div>
		    </div>
		</div>

		<div class="modal fade" id="mdl-branch-assign" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mdl-branch-assign" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered mw-550px">
		        <div class="modal-content"></div>
		    </div>
		</div>
		@endif

		<!--begin::Javascript-->
		<script>
			var hostUrl = "{{ asset('assets') }}"

			let dttbSrcTranslation = hostUrl + "/plugins/custom/datatables/Indonesia.json"
			let $_isMultiTenants = "{{ isMultiTenants() }}"
		</script>

		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<!--end::Global Javascript Bundle-->

		<!--begin::Vendors Javascript(used for this page only)-->
		<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
		<!--end::Vendors Javascript-->

		<!--begin::Custom Javascript(used for this page only)-->
		<script src="{{ asset('assets/js/custom/customs.js') }}"></script>
		<script src="{{ asset('assets/js/custom/popUp.js') }}"></script>
        <script src="{{ asset('assets/js/lang/select2/id.js') }}"></script>

        <script type="text/javascript" src="{{ asset('assets/js/custom/accounting/accounting.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/custom/accounting/accounting.min.js') }}"></script>

        <script type="text/javascript">
            $.fn.select2.defaults.set('language', 'id')

            let initBranch = false
            let branch = "{{ Auth::user()->branch->bid }}";

		    function setBranch ()
		    {
			    if (initBranch) return localStorage.getItem('auth_branch')

			    // Simpan cabang aktif ke localStorage
				localStorage.setItem('auth_branch', branch)

				initBranch = true

				return branch
		    }

		    // Handler error global AJAX
            $(document).ajaxError(function (event, xhr, settings, thrownError)
            {
            	// Abaikan request yang dibatalkan manual karena mismatch cabang
			    if (settings.__branchChecked && xhr.status === 0)
			    {
			        console.warn('Request dibatalkan manual karena branch mismatch')

			        return
			    }

			    if (xhr.status === 401)
			    {
			        let response = {}

			        try
			        {
			            response = JSON.parse(xhr.responseText)
			        }
			        catch (e) {
			        	console.warn("Invalid JSON on 401 response", xhr.responseText)
			        }

			        const { data, message } = response

			        if (data && data.authenthicated === false)
			        {
			            swalShowMessage("Informasi", message, 'warning')
			            .then(() => {
			                const $formLogout = $('#form-logout')

			                localStorage.removeItem('auth_branch')

			                $formLogout.find('[name="intended"]').val(window.location.href).end().submit()
			            })
			        }
			    }
			})

            // Tambahkan header dan validasi branch ke semua AJAX
			$.ajaxPrefilter(function (options, originalOptions, jqXHR)
			{
				const clientBranch = localStorage.getItem('auth_branch') || branch

				// Inject custom header
				options.headers = options.headers || {}
				options.headers['X-BRANCH'] = clientBranch

				// Flag khusus agar error handler tahu ini branch-guard
				options.__branchChecked = true

				// Validasi cabang vs session
				if (branch !== clientBranch)
				{
					// Batalkan request dan beri notifikasi
					jqXHR.abort()

					swalShowMessage("Peringatan", "Cabang aktif tidak cocok, halaman akan dimuat ulang.", 'warning')
			        .then(() => {
			            location.reload()
			        })
				}
			})

			// Sinkronisasi antar tab: jika auth_branch dihapus dari tab lain
			window.addEventListener('storage', function (event)
			{
				if (event.key === 'auth_branch' && !localStorage.getItem('auth_branch'))
				{
					swalShowMessage("Informasi", "Cabang dihapus dari tab lain. Halaman akan dimuat ulang.", 'info')
			        .then(() => {
			            location.reload()
			        })
				}
			}, false)

    		setBranch()

            @if ($_isMultiTenants == 't')
	            $(document).ready(function ()
	            {
	            	const INIT_SELECT_BRANCH = "{{ session('__openBranch__') }}"
	            	const INIT_OPEN_KONFIGURASI = "{{ session('__openKonfigurasi__') }}"

	            	if (INIT_SELECT_BRANCH == 't')
	            		$('.ShowModalBranch').trigger('click')
	            })

	            $('.ShowModalBranch').on('click', function (e)
	            {
	                e.preventDefault()

	                $.ajax({
	                    url         : "{{ route('api.auth.change_branch') }}",
	                    data        : { },
	                    type        : 'GET',

	                    error       : function (req, stat, err)
	                                {
	                                    swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
	                                },

	                    success     : function (response)
	                                {
	                                    openModal('popup-branch', response, null, true)
	                                },

	                    async       : false,
	                    cache       : false
	                })
	            })
			@endif

            $('.konfigurasi-akun-modal').on('click', function (e)
            {
                e.preventDefault()

                $.ajax({
                    url         : "{{ route('api.auth.change_password') }}",
                    data        : { },
                    type        : 'GET',

                    error       : function (req, stat, err)
                                {
                                    swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
                                },

                    success     : function (response)
                                {
                                    openModal('konfigurasi-akun-modal', response, null, true)
                                },

                    async       : false,
                    cache       : false
                })
            })

            $('.proses-logout').on('click', function (e)
            {
                e.preventDefault()

                Swal.fire({
                    html: 'Apakah Anda Yakin <span class="badge badge-danger">Logout Aplikasi</span> ?',
                    icon: "info",
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: "Ya, Logout !",
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-dark",
                        cancelButton: 'btn btn-danger'
                    }
                }).then((result) =>
                {
                    if (result.isConfirmed) $('#form-logout').submit()
                })
            })

            function modalAuth (otogid, frm, notes)
		    {
		        $.ajax({
		            url         : "{{ route('api.auth.otorisasi_akses') }}",
		            data        : { otogid: otogid, frm: frm, notes: notes },
		            type        : 'GET',

		            error       : function (req, stat, err)
		                        {
		                            swalShowMessage('Gagal', 'Gagal membuka form.', 'error')
		                        },

		            success     : function (response)
		                        {
		                            openModal('popup_auth_user', response, null, true)
		                        },

		            async       : false,
		            cache       : false
		        })
		    }

            /*function MyInbox ()
            {
		      	MyNotif()
		      	ListPesan()

		      	setTimeout('MyInbox()', 5000)
		    }

		    function MyNotif ()
		    {
		    	$.ajax({
		        	url 	: "{{ route('api.mymail.myinbox') }}",
		        	data 	: { first_new: true, notification: true },

		        	success : function (json)
		        			{
		          				const mails = json.data
		          				const $container = $('.mymail-notif')

		          				$container.html('')

		          				if (mails.length === 0)
		          				{
		          					const $row_kosong = `<div class="d-flex flex-column align-items-center">
				                                            <img src="{{ asset('assets/media/illustrations/sigma-1/5.png') }}" class="image-fluid h-125px">
				                                            <span class="fs-7 text-gray-800 fw-bold my-5">belum ada laporan baru</span>
				                                        </div>`

		            				$container.append($row_kosong)
		          				}

		          				for (const mail of mails)
		          				{
		            				const bgLight = ''

		            				let $row_mail = `<div class="mymail-notif-item d-flex align-items-center justify-content-between px-9 py-4 ${bgLight}" data-lid="${mail.lid}">
									                	<div class="d-flex align-items-start">
									                  		<div class="symbol symbol-35px me-4">
											                    <span class="symbol-label bg-light-dark">
									                      			<i class="las la-info-circle text-dark" style="font-size: 16px;"></i>
									                    		</span>
									                  		</div>

									                  		<div>
									                    		<div class="fs-8 text-gray-800 fw-bolder">Ada Pengaduan</div>
									                  		</div>
									                	</div>

									                	<div class="badge badge-light-success rounded-1 cursor-pointer show-detail d-none" data-lid="${mail.lid}">
									                  		Lihat
									                	</div>
									              	</div>

									              	<audio controls autoplay loop style="display: none;">
													  	<source src="{{ asset('assets/sound/notif.mp3') }}" type="audio/mpeg">
													  	Your browser does not support the audio element.
													</audio>`

						            $container.append($row_mail)
		          				}
		        			}
		      	})
		    }

		    function ListPesan ()
		    {
		    	$.ajax({
		        	url 	: "{{ route('api.mymail.myinbox.list') }}",
		        	data 	: { first_new: true, notification: true },

		        	success : function (json)
		        			{
		          				const mails = json.data
		          				const $container = $('.list-mymail-notif')

		          				const $button_more = `<div class="px-9 mt-3">
					              						<a href="{{ route('homepage') }}" class="btn btn-sm btn-dark rounded-1 w-100 mt-1">Lihat Lebih Banyak</a>
					            					</div>`

		          				$container.html('')

		          				if (mails.length === 0)
		          				{
		          					const $row_kosong = `<div class="d-flex flex-column align-items-center">
				                                            <img src="{{ asset('assets/media/illustrations/sigma-1/5.png') }}" class="image-fluid h-125px">
				                                            <span class="fs-7 text-gray-800 fw-bold my-5">belum ada laporan baru</span>
				                                        </div>`

		            				$container.append($row_kosong)
		          				}

		          				for (const mail of mails)
		          				{
		            				const bgLight = mail.lapor_verif == 't' ? '' : 'bg-light-dark'

		            				let $row_mail = `<div class="list-mymail-notif-item d-flex align-items-center justify-content-between px-9 py-4 ${bgLight}" data-lid="${mail.lid}">
									                	<div class="d-flex align-items-start">
									                  		<div class="symbol symbol-35px me-4">
											                    <span class="symbol-label bg-light-dark">
									                      			<i class="las la-info-circle text-dark" style="font-size: 16px;"></i>
									                    		</span>
									                  		</div>

									                  		<div>
									                    		<div class="fs-8 text-gray-800 fw-bolder">${mail.nama_jenis}</div>
									                    		<div class="text-gray-400 fs-8">${moment(mail.create_time).format('DD/MM/YYYY - HH:mm')} dari ${mail.is_mobile}</div>
									                  		</div>
									                	</div>

									                	<div class="badge badge-light-success rounded-1 cursor-pointer show-detail d-none" data-lid="${mail.lid}">
									                  		Lihat
									                	</div>
									              	</div>`

						            $container.append($row_mail)
		          				}

	          					$container.append($button_more)
		        			}
		      	})
		    }

		    MyInbox()*/
        </script>

        @stack('script')
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>