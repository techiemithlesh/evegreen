<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>
    <link rel="icon" type="image/png"  href="{{ asset('assets/images/title.png') }}">
    <!-- CSS Section -->
    <!-- Line Icons CSS (General Icons) -->
    <!-- <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" /> -->

    <!-- Bootstrap CSS (Core Styling) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <!-- jQuery UI CSS (for date picker and draggable elements if needed) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

    <!-- DataTables Row Reorder CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.3.3/css/rowReorder.dataTables.min.css">

    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <!-- JS Section -->
    <!-- jQuery (Required by DataTables, Select2, jQuery UI) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Bootstrap JS (Ensure it's the bundle with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery Validation JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

    <!-- DataTables Core JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- DataTables Buttons JS (Export Functionality) -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> <!-- Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> <!-- PDF Export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script> <!-- CSV, Excel, PDF Export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script> <!-- Print Button -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- DataTables Select Extension JS -->
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

    <!-- DataTables Row Reorder Extension JS -->
    <script src="https://cdn.datatables.net/rowreorder/1.3.3/js/dataTables.rowReorder.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom JS -->
    <script src="{{asset('js/common.js')}}"></script>
    <style>
        /* Thin, rounded scrollbar */
        #sidebar::-webkit-scrollbar {
            width: 8px; /* Adjust scrollbar width */
        }

        /* Scrollbar track (background) */
        #sidebar::-webkit-scrollbar-track {
            background: transparent; /* Fully transparent */
            border-radius: 10px;
        }

        /* Scrollbar handle (thumb) */
        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(136, 136, 136, 0.5); /* Semi-transparent */
            border-radius: 10px; /* Rounded corners */
            transition: background 0.3s ease-in-out; /* Smooth animation */
        }

        /* Scrollbar handle on hover */
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(85, 85, 85, 0.8); /* Darker on hover */
        }

    </style>
    <script>
        function resetTimer() {
            $.ajax({
                method:"post",
                url:"{{route('activity.test')}}",
                success:function(response){
                    if(!response.status){
                        window.location.href = "{{ route('logout') }}"; 
                    }
                },
                error:function(errors){
                    console.log(errors);
                }

            })
        }
        $(document).ready(function() {
            setInterval(resetTimer, 60000);
        })
    </script>
 
</head>
<x-confirmation />
<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-toggle text-sm" style="overflow-y: scroll;max-height:100vh;">
            <div class="sidebar-logo" style="border-bottom: 3px solid white;">
                <a href="#">Product Management</a>
            </div>
            <!-- Sidebar Navigation -->
                <a id="p42" onclick="navBarMenuActive(0, 0 , 0);" href="{{url('/home')}}" class="sidebar-link show">
                    <i class="fa fa-home"></i> 
                    <span>Dashboard</span>
                </a>
            <?php

            use Illuminate\Support\Facades\Redis;

            $user = auth()->user();
            $menuList = json_decode(Redis::get("menu_list_" . $user["user_type_id"]), true);
            $tree = mapTree($menuList, 0);
            echo ($tree);
            ?>
            <!-- Sidebar Navigation Ends -->
            <div class="sidebar-footer">
                <a href="#" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Setting</span>
                </a>
            </div>
        </aside>
        <!-- Sidebar Ends -->
         
        <div class="main" style="overflow-y: scroll;max-height:100vh;">
            <div id="loadingDiv" style="background: url('{{ asset('assets/loaders/d.gif') }}') no-repeat center center; position: absolute; top: 10%;  height: 90vh; width: 80vw; z-index: 999999999999;"></div>
            <nav class="navbar navbar-expand">
                <button class="toggler-btn" type="button">
                    <i class="bi bi-justify"></i>
                </button>



                <nav class="navbar1 navbar-expand  text-white py-2">
                    <div class="container-fluid d-flex align-items-center">
                        <!-- Left Section (Toggle button) -->
                        <div class="navbar-left">
                            <button class="navbar-toggler border-0 bg-transparent text-white" type="button">
                                <i class="lni lni-text-align-left"></i>
                            </button>
                        </div>


                        <!-- Right Section (Icons) -->
                        <div class="navbar-icons d-flex gap-3 ">
                            <a href="#" class="text-white" title="Notifications">
                                <i class="bi bi-bell"></i>
                            </a>
                            <!-- User Dropdown -->
                            <div class="dropdown">
                                <a href="#" class="text-white dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="User Menu">
                                    <i class="bi bi-person"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right bg-white text-dark" aria-labelledby="userDropdown" style="margin-left: -55px;">
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('profile')}}"><i class="bi bi-person-fill text-primary"></i> Profile</a>
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('change-password')}}"><i class="bi bi-pencil-square text-primary"></i> Change Password</a>
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('logout')}}"><i class="bi bi-power text-primary"></i> Logout</a>
                                </div>
                            </div>

                            <a href="#" class="text-white" title="Settings">
                                <i class="bi bi-three-dots"></i>
                            </a>
                            <a href="#" class="text-white" title="Settings">

                            </a>
                            <a href="#" class="text-white" title="Settings">

                            </a>
                        </div>
                    </div>
                </nav>



            </nav>