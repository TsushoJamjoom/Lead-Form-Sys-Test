<div class="map-outer" wire:ignore  x-data="{expanded : false}" :style="expanded ? 'overflow : visible' : ''">
    <div class="card mt-3">
        <div class="card-header">
            @if (!empty($salesUserId))
                <a class="btn btn-secondary float-end mx-2" href="{{ route('map') }}" >
                    Clear
                </a>
            @endif
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = !expanded">
                Apply Filter
            </button>
        </div>
        @php
            $isSalesUser = \App\Helpers\AppHelper::isSalesDeptUser($this->user);
            $isStaffUser = \App\Helpers\AppHelper::isStaffUser($this->user);
        @endphp
        <div class="collapse {{$isCollapse}}" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    @if (!$isSalesUser || !$isStaffUser)
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                            <label>Sales User</label>
                            <x-select2 class="form-control"  name="salesUserId" id="salesUserId" parentId="collapseExample">
                                <option value="">Search...</option>
                                <option value="0">All</option>
                                @foreach ($this->salesUsers as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                            </x-select2>
                        </div>
                        @endif
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2 mt-4">
                            <button type="button" class="btn btn-outline-primary btn-width"
                                wire:click="submitFilter">Submit</button>
                                <a href="{{route('map')}}" class="btn btn-outline-danger btn-width">Reset</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div id="map"></div>
<!-- Modal -->
    <div class="modal fade" id="appointmentModal" aria-labelledby="appointmentModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        // dd($companyid);
                    @endphp
                    <form>
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <label for="company_id">Company</label>
                                <select class="form-select" id="company_id" wire:model="companyid">
                                    <option value="">Select Company</option>
                                    @foreach ($this->companyDropDown as $data)
                                        <option value="{{ $data->id }}">{{ $data->company_name }} -
                                            {{ $data->customer_code }}</option>
                                    @endforeach
                                </select>
                                @error('companyid')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="visit_date">Visit Date</label>
                                <input type="date" id="visit_date" class="form-control" wire:model="visit_date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                @error('visit_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="visit_time">Visit Time</label>
                                <input type="time" id="visit_time" class="form-control" wire:model="visit_time">
                            </div>
                        </div>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                        <button type="button" wire:click="createAppointment" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
        </div>
    </div>
</div>
<script async src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}&loading=async&callback=initMap"></script>
<script>
    // Sample data: list of locations with lat, long, title, description, and link
let locations = @json($this->listData);

// Initialize and add the map
    async function initMap() {
        // The map, centered at the first location
        var mapOptions = {
            zoom: 5,
            center: new google.maps.LatLng("22.8384486", "41.7555768")
        };

        const map = new google.maps.Map(document.getElementById("map"), mapOptions);

        // Add markers to the map
        locations.forEach(location => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(location.lat), lng: parseFloat(location.lng) },
                map: map,
                title: location.title,
            });

            // Info window content
            const contentString = `
                <div>
                    <h4>${location.title} ${location.visit_symbol} ${location.active_symbol}</h4>
                    <h5>Customer Code: ${location.description}</h5>
                    <a class="btn btn-warning" href="${location.link}" target="_blank">View</a> &nbsp;
                    <button class="btn btn-primary" onclick="getDirections(${location.lat}, ${location.lng})">Get Directions</button>
                    <button type="button" class="btn btn-success" onclick="triggerCreateEvent(${location.company_id})">Book Appointment</button> 
                    &nbsp;
                    ${location.logo ? `<img src="${location.logo}" style="height: 80px;width: 80px">` : ''}
                     <br>
                    ${location.sales_user_profile_photo ? `<img src="${location.sales_user_profile_photo}" alt="${location.sales_user_name}" style="height: 80px;width: 80px">` : ''}
                </div>
            `;


            const infowindow = new google.maps.InfoWindow({
                content: contentString,
            });

            // Add click event to open info window on marker click
            marker.addListener("click", () => {
                infowindow.open(map, marker);
            });
        });

        $('.map-outer').css('overflow','');
    }

    function getDirections(lat, lng) {
        // Use browser geolocation to get user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                const directionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${lat},${lng}&travelmode=driving`;
                window.open(directionsUrl, '_blank');
            }, error => {
                alert("Geolocation failed. Please try again.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function triggerCreateEvent(companyId) {
        Livewire.dispatch('triggerCreateEvent',{
            companyId: companyId
            });
    }

    
    
    // Load the map asynchronously
    (async () => {
        await initMap();
    })();
    
</script>
@script
<script>
    $wire.on('showCreateEventModal', (event) => {
        const companyId = event[0].companyId;
        $('#company_id').val(companyId);
        $('#company_id').attr('disabled',true);
        $('#appointmentModal').modal('show');
    });
</script>
@endscript
