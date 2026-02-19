@extends('layouts.app')

@section('title', __('Empleados'))


@php
    $group = current_group();
    $groupCode = current_group_code() ?? (request()->route('group') ?? 'PE');
    $breadcrumbs = [
        'title' => '',
        'description' => '',
        'icon' => 'ti tabler-users',
        'items' => [
            ['name' => 'RRHH', 'url' => url(app()->getLocale() . '/' . $groupCode . '/hr')],
            ['name' => __('Gestión de personal')],
            ['name' => __('Empleados')],
            ['name' => __('Ficha del empleado')],
        ],
    ];
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="$breadcrumbs">
        <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                @if ($group)
                    <span class="badge bg-label-primary" title="{{ $group->business_name ?? $group->trade_name }}">
                        <i class="ti tabler-map-pin me-1"></i>{{ $group->code }}
                    </span>
                @endif
                <span class="badge bg-label-info">
                    <i class="ti tabler-users"></i>
                </span>
            </div>
        </x-slot:extra>
    </x-breadcrumbs>
@endsection

@section('content')
    <div>
        {{--  --}}
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class=" d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mb-4" src="{{ asset('vuexy/img/avatars/4.png') }}" height="120"
                                    width="120" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h5>Violet Mendoza</h5>
                                    <span class="badge bg-label-secondary">EMP-001</span>
                                    <span class="badge bg-label-info">Super Administrador</span>
                                    <span class="badge bg-label-success">ACTIVO</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-checkbox icon-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">S/ 10,00.23</h5>
                                    <span>Ventas</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-briefcase icon-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">568</h5>
                                    <span>Clientes</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4">Detalles</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">COD:</span>
                                    <span>EMP-001</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Tipo documento:</span>
                                    <span>DNI</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Nro documento:</span>
                                    <span>73509506</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Empresa:</span>
                                    <span></span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Correo:</span>
                                    <span>joel@gmail.com</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Estado:</span>
                                    <span>Activo</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Rol:</span>
                                    <span>Super Administrador</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Teléfono:</span>
                                    <span>940 588 829</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Idioma:</span>
                                    <span>Español</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Pais:</span>
                                    <span>Perú</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:;" class="btn btn-primary me-4" data-bs-target="#editUser"
                                    data-bs-toggle="modal">Editar</a>
                                <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspender</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 order-0 order-md-1">
                <!-- User Pills -->
                <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-user-check icon-sm me-1_5"></i>Cuenta</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="app-user-view-security.html"><i
                                    class="icon-base ti tabler-lock icon-sm me-1_5"></i>Seguridad</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-credit-card icon-sm me-1_5"></i>Pagos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-bell icon-sm me-1_5"></i>Notificaciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-history icon-sm me-1_5"></i>Historial laboral</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-link icon-sm me-1_5"></i>Conexiones</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);"><i
                                    class="icon-base ti tabler-folder icon-sm me-1_5"></i>Documentos</a>
                        </li>
                    </ul>
                </div>
                <!--/ User Pills -->

                <!-- Project table -->
                <div class="card mb-6">
                    <div class="table-responsive mb-4">
                        <table class="table datatable-project">
                            <thead class="border-top">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Project</th>
                                    <th>Leader</th>
                                    <th>Team</th>
                                    <th class="w-px-200">Progress</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /Project table -->

                <!-- Activity Timeline -->
                <div class="card mb-6">
                    <h5 class="card-header">User Activity Timeline</h5>
                    <div class="card-body pt-1">
                        <ul class="timeline mb-0">
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">12 Invoices have been paid</h6>
                                        <small class="text-body-secondary">12 min ago</small>
                                    </div>
                                    <p class="mb-2">Invoices have been paid to the company</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-lighter rounded d-flex align-items-center">
                                            <img src="../../assets//img/icons/misc/pdf.png" alt="img"
                                                width="15" class="me-2" />
                                            <span class="h6 mb-0 text-body">invoices.pdf</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-success"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Client Meeting</h6>
                                        <small class="text-body-secondary">45 min ago</small>
                                    </div>
                                    <p class="mb-2">Project meeting with john @10:15am</p>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                        <div class="d-flex flex-wrap align-items-center mb-50">
                                            <div class="avatar avatar-sm me-2">
                                                <img src="../../assets/img/avatars/1.png" alt="Avatar"
                                                    class="rounded-circle" />
                                            </div>
                                            <div>
                                                <p class="mb-0 small fw-medium">Lester McCarthy (Client)</p>
                                                <small>CEO of Pixinvent</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-info"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Create a new project for client</h6>
                                        <small class="text-body-secondary">2 Day Ago</small>
                                    </div>
                                    <p class="mb-2">6 team members in a project</p>
                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <ul
                                                    class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Vinnie Mostowy"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle"
                                                            src="../../assets/img/avatars/5.png" alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Allen Rieske"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle"
                                                            src="../../assets/img/avatars/12.png" alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Julee Rossignol"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle"
                                                            src="../../assets/img/avatars/6.png" alt="Avatar" />
                                                    </li>
                                                    <li class="avatar">
                                                        <span class="avatar-initial rounded-circle pull-up"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="3 more">+3</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /Activity Timeline -->

                <!-- Invoice table -->
                <div class="card mb-4">
                    <div class="card-datatable table-responsive">
                        <table class="table datatable-invoice">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Issued Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /Invoice table -->
            </div>
            <!--/ User Content -->
        </div>

        <!-- Modal -->
        <!-- Edit User Modal -->
        <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h4 class="mb-2">Edit User Information</h4>
                            <p>Updating user details will receive a privacy audit.</p>
                        </div>
                        <form id="editUserForm" class="row g-6" onsubmit="return false">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserFirstName">First Name</label>
                                <input type="text" id="modalEditUserFirstName" name="modalEditUserFirstName"
                                    class="form-control" placeholder="John" value="John" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserLastName">Last Name</label>
                                <input type="text" id="modalEditUserLastName" name="modalEditUserLastName"
                                    class="form-control" placeholder="Doe" value="Doe" />
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="modalEditUserName">Username</label>
                                <input type="text" id="modalEditUserName" name="modalEditUserName"
                                    class="form-control" placeholder="johndoe007" value="johndoe007" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserEmail">Email</label>
                                <input type="text" id="modalEditUserEmail" name="modalEditUserEmail"
                                    class="form-control" placeholder="example@domain.com"
                                    value="example@domain.com" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserStatus">Status</label>
                                <select id="modalEditUserStatus" name="modalEditUserStatus"
                                    class="select2 form-select" aria-label="Default select example">
                                    <option selected>Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                    <option value="3">Suspended</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditTaxID">Tax ID</label>
                                <input type="text" id="modalEditTaxID" name="modalEditTaxID"
                                    class="form-control modal-edit-tax-id" placeholder="123 456 7890"
                                    value="123 456 7890" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserPhone">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">US (+1)</span>
                                    <input type="text" id="modalEditUserPhone" name="modalEditUserPhone"
                                        class="form-control phone-number-mask" placeholder="202 555 0111"
                                        value="202 555 0111" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserLanguage">Language</label>
                                <select id="modalEditUserLanguage" name="modalEditUserLanguage"
                                    class="select2 form-select" multiple>
                                    <option value="">Select</option>
                                    <option value="english" selected>English</option>
                                    <option value="spanish">Spanish</option>
                                    <option value="french">French</option>
                                    <option value="german">German</option>
                                    <option value="dutch">Dutch</option>
                                    <option value="hebrew">Hebrew</option>
                                    <option value="sanskrit">Sanskrit</option>
                                    <option value="hindi">Hindi</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserCountry">Country</label>
                                <select id="modalEditUserCountry" name="modalEditUserCountry"
                                    class="select2 form-select" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Canada">Canada</option>
                                    <option value="China">China</option>
                                    <option value="France">France</option>
                                    <option value="Germany">Germany</option>
                                    <option value="India" selected>India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Korea">Korea, Republic of</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Russia">Russian Federation</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="editBillingAddress" />
                                    <label for="editBillingAddress" class="switch-label">Use as a billing
                                        address?</label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Edit User Modal -->

        <!-- Add New Credit Card Modal -->
        <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-simple modal-upgrade-plan">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h2>Upgrade Plan</h2>
                            <p class="text-body-secondary">Choose the best plan for user.</p>
                        </div>
                        <form id="upgradePlanForm" class="row g-4" onsubmit="return false">
                            <div class="col-sm-9">
                                <label class="form-label" for="choosePlan">Choose Plan</label>
                                <select id="choosePlan" name="choosePlan" class="form-select"
                                    aria-label="Choose Plan">
                                    <option selected>Choose Plan</option>
                                    <option value="standard">Standard - $99/month</option>
                                    <option value="exclusive">Exclusive - $249/month</option>
                                    <option value="Enterprise">Enterprise - $499/month</option>
                                </select>
                            </div>
                            <div class="col-sm-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Upgrade</button>
                            </div>
                        </form>
                    </div>
                    <hr class="mx-md-n5 mx-n3" />
                    <div class="modal-body">
                        <h6 class="mb-0">User current plan is standard plan</h6>
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex justify-content-center me-2 mt-1">
                                <sup class="h6 pricing-currency pt-1 mt-2 mb-0 me-1 text-primary">$</sup>
                                <h1 class="mb-0 text-primary">99</h1>
                                <sub class="pricing-duration mt-auto mb-5 pb-1 small text-body">/month</sub>
                            </div>
                            <button class="btn btn-label-danger cancel-subscription">Cancel Subscription</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Add New Credit Card Modal -->

        <!-- /Modal -->

    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vuexy/vendor/css/pages/page-user-view.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('vuexy/js/modal-edit-user.js') }}"></script>
    <script src="{{ asset('vuexy/js/app-user-view.js') }}"></script>
    <script src="{{ asset('vuexy/js/app-user-view-account.js') }}"></script>
@endpush
