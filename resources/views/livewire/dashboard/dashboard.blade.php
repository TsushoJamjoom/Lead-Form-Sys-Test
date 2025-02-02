<div class="container mt-5">
    <style>
        .table td,
        .table th {
            border: none;
        }

        .outer-circle {
            display: inline-block;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            position: relative;
            background: conic-gradient(
                green calc(var(--percentage) * 1%),  /* The filled part */
                #ccc 0 /* The unfilled part */
            );
            cursor: pointer;
        }

        /* Tooltip on hover */
        .outer-circle::after {
            content: attr(data-percentage) '%';  /* Show the percentage value */
            position: absolute;
            top: -30px;  /* Position it above the circle */
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        /* Show the tooltip on hover */
        .outer-circle:hover::after {
            opacity: 1;
        }

        .inner-circle {
            display: inline-block;
            width: 57px;
            height: 57px;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            text-align: center;
            line-height: 57px;
            font-weight: bold;
            border: 2px solid #ccc;
            color: black;
        }
        .card canvas {
            transform: none;
            transition: none;
        }
    </style>
    <!-- Filters -->
    <div class="card mb-4" x-data="{expanded : false}">
        <div class="card-header">
            <button class="btn btn-success float-end ms-2 d-none" type="button" wire:click="exportFile">
                Export
            </button>
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = !expanded">
                Apply Filter
            </button>
        </div>
        <div class="collapse {{ $isCollapse }}" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Filter By</label>
                        <select class="form-select" wire:model="filterBy" required>
                            <option value="all">All</option>
                            <option value="customer">Customer</option>
                            <option value="month_visit">Month Visit Activity</option>
                            <option value="sales_lead_ticket">SalesLead and Ticket Activity</option>
                            <option value="satisfaction_scale">Satisfaction Scale Rate</option>
                            <option value="sales_performance">Sales Performance</option>
                            <option value="tickets">Tickets</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Period</label>
                        <x-date-range-filter class="form-control"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Reigon/branch</label>
                        <select class="form-select" wire:model="branchId" required>
                            <option value="">Select Reigon/branch</option>
                            @foreach ($this->branchList as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Category</label>
                        <select class="form-select" wire:model="categoryBy" required>
                            <option value="">Select Category</option>
                            <option value="2">High Frequency</option>
                            <option value="1">Low Frequency</option>
                            <option value="0">Unattended</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Department</label>
                        <select class="form-select" wire:model="departmentId" required>
                            <option value="">All</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @php
                        $isStaffUser = App\Helpers\AppHelper::isStaffUser($this->user);
                        $isSalesDeptUser = App\Helpers\AppHelper::isSalesDeptUser($this->user);
                    @endphp
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        @if (!$isStaffUser || !$isSalesDeptUser)
                            <label>User</label>
                            <x-select2 class="form-control" name="userId" id="userId" parentId="collapseExample">
                                <option value="">Search...</option>
                                @foreach ($this->userList as $usr)
                                    <option value="{{ $usr->id }}" {{ $usr->id == $userId ? 'selected' : '' }}>
                                        {{ $usr->name }}</option>
                                @endforeach
                            </x-select2>
                        @endif
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2 mt-4 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-width"
                            wire:click="clear">Reset</button>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2 mt-4">
                        <button type="button" class="btn btn-outline-primary btn-width"
                            wire:click="submitFilter">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Customers -->
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="text-center mt-2">Customers</h6>
                </div>
                <div class="card-body">
                    <canvas id="customersChart" class="chart-size" wire:ignore.self></canvas>
                </div>
            </div>
        </div>
        <!-- End Customers -->
        <!-- Month Visit Activity -->
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="text-center mt-2">Month Visit Activity</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthVisitActivityChart" class="chart-size" wire:ignore.self></canvas>
                </div>
            </div>
        </div>
        <!-- End Month Visit Activity -->
        <!-- SalesLead and Ticket Activity -->
        <div class="col-sm-12 col-md-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="text-center mt-2">SalesLead and Ticket Activity</h6>
                </div>
                <div class="card-body">
                    <canvas id="createdActivityChart" class="chart-size" wire:ignore.self></canvas>
                </div>
            </div>
        </div>
        <!-- End SalesLead and Ticket Activity -->
        <!-- Satisfaction Scale Rate -->
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="text-center mt-2">Satisfaction Scale Rate</h6>
                </div>
                <div class="card-body" style="min-height: 350px;">
                    <canvas id="satisfactionScaleChart" class="chart-size" wire:ignore.self></canvas>
                </div>
            </div>
        </div>
        <!-- End Satisfaction Scale Rate -->
        <!-- Tickets -->
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="text-center mt-2">Tickets (Bar colors by department)</h6>
                </div>
                <div class="card-body" style="min-height: 350px;">
                    <canvas id="ticketsChart" class="chart-size" wire:ignore.self></canvas>
                </div>
            </div>
        </div>
        <!-- End Tickets -->
        <!-- Sales Performance -->
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h6 class="text-center mt-2">Sales Performance</h6>
                        <button class="btn btn-success" wire:click="exportFile">Export</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive max-height-500">
                        <table class="table table-bordered text-center">
                            <thead class="thead-dark" style="background-color: skyblue;color:white">
                                <tr>
                                    <th scope="col">Profile Photo</th>
                                    <th scope="col">Executive Name</th>
                                    <th scope="col">Assigned Customers</th>
                                    <th scope="col">Total Visits</th>
                                    <th scope="col">High Frequency</th>
                                    <th scope="col">Low Frequency</th>
                                    <th scope="col">Unattended</th>
                                    <th scope="col">Sales Lead</th>
                                    <th scope="col">Pending Tickets</th>
                                    <th scope="col">Achieved Saleslead</th>
                                    <th scope="col">Lost Saleslead</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($this->salesPerformance))
                                    @foreach($this->salesPerformance as $item)
                                        <tr>

                                            <td><img src="{{$item['profile_photo']}}"  class="outer-circle" alt="Cinque Terre"></td>
                                            <td class="text-black bolt">{{ $item['name'] }}</td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('company_count'),$item['company_count'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('company_count'),$item['company_count'])}};">
                                                    <div class="inner-circle">{{ $item['company_count'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('events_count'),$item['events_count'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('events_count'),$item['events_count'])}};">
                                                    <div class="inner-circle">{{ $item['events_count'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('highFrequency'),$item['highFrequency'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('highFrequency'),$item['highFrequency'])}};">
                                                    <div class="inner-circle">{{ $item['highFrequency'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('lowFrequency'),$item['lowFrequency'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('lowFrequency'),$item['lowFrequency'])}};">
                                                    <div class="inner-circle">{{ $item['lowFrequency'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('unattended'),$item['unattended'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('unattended'),$item['unattended'])}};">
                                                    <div class="inner-circle">{{ $item['unattended'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('salesleads_count'),$item['salesleads_count'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('salesleads_count'),$item['salesleads_count'])}};">
                                                    <div class="inner-circle">{{ $item['salesleads_count'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('ticketCount'),$item['ticketCount'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('ticketCount'),$item['ticketCount'])}};">
                                                    <div class="inner-circle">{{ $item['ticketCount'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('totalArchivedSalesLead'),$item['totalArchivedSalesLead'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('totalArchivedSalesLead'),$item['totalArchivedSalesLead'])}};">
                                                    <div class="inner-circle">{{ $item['totalArchivedSalesLead'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="outer-circle" data-percentage="{{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('totalLostSalesLead'),$item['totalLostSalesLead'])}}" style="--percentage: {{App\Helpers\AppHelper::getPercentageValue(collect($this->salesPerformance)->sum('totalLostSalesLead'),$item['totalLostSalesLead'])}};">
                                                    <div class="inner-circle">{{ $item['totalLostSalesLead'] }}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td>{{collect($this->salesPerformance)->sum('company_count')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('events_count')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('highFrequency')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('lowFrequency')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('unattended')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('salesleads_count')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('ticketCount')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('totalArchivedSalesLead')}}</td>
                                        <td>{{collect($this->salesPerformance)->sum('totalLostSalesLead')}}</td>
                                    </tr>
                                 @else
                                    <tr>
                                        <td colspan="8" class="text-center">No Record found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sales Performance -->
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customersChartCtx = document.getElementById('customersChart').getContext('2d');
            const customersChartChart = new Chart(customersChartCtx, {
                type: 'bar',
                data: {
                    labels: ['High Frequency', 'Low Frequency', 'Unattended', 'Total'],
                    datasets: [{
                        label: '',
                        data: @json($this->customerData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    hover: {
                        mode: null
                    }
                },
                plugins: [ChartDataLabels],
            });

            const monthVisitActivityCtx = document.getElementById('monthVisitActivityChart').getContext('2d');
            const monthVisitActivityChart = new Chart(monthVisitActivityCtx, {
                type: 'bar',
                data: {
                    labels: ['Planned', 'Actual', 'Missed'],
                    datasets: [{
                        label: '',
                        data: @json($this->monthVisitActivity),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    hover: {
                        mode: null
                    }
                },
                plugins: [ChartDataLabels],
            });


            const createdActivityCtx = document.getElementById('createdActivityChart').getContext('2d');
            const createdActivityChart = new Chart(createdActivityCtx, {
                type: 'bar',
                data: {
                    labels: ['Created Sales Lead', 'Created Ticket', 'Active excutives'],
                    datasets: [{
                        label: '',
                        data: @json($this->createdActivity),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    hover: {
                        mode: null
                    }
                },
                plugins: [ChartDataLabels],
            });


            const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
            const ticketsChart = new Chart(ticketsCtx, {
                type: 'bar',
                data: {
                    labels: ['Created', 'Pending', 'In Process', 'Completed'],
                    datasets: @json($this->ticketsDataset)
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            color: '#FFF',
                            font: {
                                size: '14px'
                            },
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    hover: {
                        mode: null
                    }
                },
                plugins: [ChartDataLabels],
            });

            const satisfactionScaleCtx = document.getElementById('satisfactionScaleChart').getContext('2d');
            const satisfactionScaleData = @json($this->feedbackData);
            const values = Object.values(satisfactionScaleData);

            const plugin = {
                id: 'emptyDoughnut',
                afterDraw(chart, args, options) {
                    const {
                        datasets
                    } = chart.data;
                    const {
                        color,
                        width,
                        radiusDecrease
                    } = options;
                    let hasData = false;

                    for (let i = 0; i < datasets.length; i += 1) {
                        const dataset = datasets[i];
                        hasData |= dataset.data.length > 0;
                    }

                    if (!hasData) {
                        const {
                            chartArea: {
                                left,
                                top,
                                right,
                                bottom
                            },
                            ctx
                        } = chart;
                        const centerX = (left + right) / 2;
                        const centerY = (top + bottom) / 2;
                        const r = Math.min(right - left, bottom - top) / 2;

                        ctx.beginPath();
                        ctx.lineWidth = width || 2;
                        ctx.strokeStyle = color || 'rgba(255, 128, 0, 0.5)';
                        ctx.arc(centerX, centerY, (r - radiusDecrease || 0), 0, 2 * Math.PI);
                        ctx.stroke();
                    }
                }
            };

            const satisfactionScaleChart = new Chart(satisfactionScaleCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Excellent', 'Average', 'Normal', 'Poor', 'Very Poor'],
                    datasets: [{
                        label: 'Satisfaction Scale',
                        data: values.reverse(),
                        backgroundColor: [
                            'rgba(71,212,90, 255)',
                            'rgba(243,246,143,255)',
                            'rgba(191,191,191,255)',
                            'rgba(78,149,217,255)',
                            'rgba(204,153,255,255)'
                        ],
                        borderColor: [
                            'rgba(255,255,255,255)',
                            'rgba(255,255,255,255)',
                            'rgba(255,255,255,255)',
                            'rgba(255,255,255,255)',
                            'rgba(255,255,255,255)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Satisfaction Scale Rate'
                        },
                        datalabels: {
                            color: '#000',
                            font: {
                                size: '14px'
                            },
                        },
                        emptyDoughnut: {
                            color: 'rgba(255, 128, 0, 0.5)',
                            width: 2,
                            radiusDecrease: 20
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    hover: {
                        mode: null
                    }
                },
                plugins: [ChartDataLabels, plugin],
            });
        });


    </script>
@endpush
