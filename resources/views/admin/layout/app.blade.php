<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
	@include('admin.include.head')

</head>

<body class="ec-header-fixed ec-sidebar-fixed ec-sidebar-light ec-header-light" id="body">

	<!--  WRAPPER  -->
	<div class="wrapper">
		
		<!-- LEFT MAIN SIDEBAR -->
		@include('admin.include.sidebar')

		<!--  PAGE WRAPPER -->
		<div class="ec-page-wrapper">

			<!-- Header -->
			@include('admin.include.header')

			<!-- CONTENT WRAPPER -->
			@yield('content')
            <!-- End Content Wrapper -->

			<!-- Footer -->
			@include('admin.include.footer')

		</div> <!-- End Page Wrapper -->
	</div> <!-- End Wrapper -->

	@include('admin.include.script')
</body>

</html>