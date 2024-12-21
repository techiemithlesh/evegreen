<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>
    <!-- Line Icons CSS -->
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <!-- jQuery (required for DataTables and Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (ensure it's the bundle with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- jQuery Validation JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons JS (for export functionality) -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> <!-- Required for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> <!-- Required for PDF export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script> <!-- Required for CSV, Excel, PDF export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script> <!-- Required for print functionality -->

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Select Extension JS -->
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <!-- Row Reorder Extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.3.3/css/rowReorder.dataTables.min.css">
    <!-- Row Reorder Extension JS -->
    <script src="https://cdn.datatables.net/rowreorder/1.3.3/js/dataTables.rowReorder.min.js"></script>

    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <script src="{{asset('js/common.js')}}"></script> 
</head>

<body>
    <div id="loadingDiv" style="background: url('{{ asset('assets/loaders/d.gif') }}') no-repeat center center; position: absolute; top: 0; left: 0; height: 100%; width: 100%; z-index: 9999999;"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-toggle text-sm">
            <div class="sidebar-logo" style="border-bottom: 3px solid white;">
                <a href="#">Product Management</a>
            </div>
            <!-- Sidebar Navigation -->
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
        <div class="main">
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
                                <i class="lni lni-alarm"></i>
                            </a>
                            <!-- User Dropdown -->
                            <div class="dropdown">
                                <a href="#" class="text-white dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="User Menu">
                                    <i class="lni lni-user"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right bg-white text-dark" aria-labelledby="userDropdown" style="margin-left: -55px;">
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('profile')}}"><i class="lni lni-user text-primary"></i> Profile</a>
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('change-password')}}"><i class="lni lni-pencil-alt text-primary"></i> Change Password</a>
                                    <a class="dropdown-item dropdown-item-sm" href="{{route('logout')}}"><i class="lni lni-lock-alt text-primary"></i> Logout</a>
                                </div>
                            </div>

                            <a href="#" class="text-white" title="Settings">
                                <i class="lni lni-more-alt"></i>
                            </a>
                            <a href="#" class="text-white" title="Settings">

                            </a>
                            <a href="#" class="text-white" title="Settings">

                            </a>
                        </div>
                    </div>
                </nav>



            </nav>