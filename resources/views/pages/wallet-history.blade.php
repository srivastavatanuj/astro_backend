@extends('../layout/' . $layout)

@section('subhead')
    <title>Wallet History</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">Wallet History</h2>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('withdrawalRequests') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('withdrawalRequests') }}"><i
                                    class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
    </div>
    @if ($totalRecords > 0)
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2 " aria-label="withdraw">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">Profile</th>
                        <th class="whitespace-nowrap">NAME</th>
						<th class="whitespace-nowrap">ContactNo</th>
                        <th class="whitespace-nowrap">Amount</th>
                        <th class="whitespace-nowrap">Date</th>
                        <th class="whitespace-nowrap">Payment Method</th>
                        <th class="whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp

                    @foreach ($wallet as $request)
                    
                    
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full"
                                            src="/{{ $request->userProfile }}"
                                            onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                            alt="Astrologer image" />
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $request->userName }}
                                   </div>
                           </td>
                                <td>
                                <div class="font-medium whitespace-nowrap">
                                   {{ $request->userContact }}</div>
                           </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $request->amount }}</div>
                            </td>
                            <td>
                                {{ date('d-m-Y', strtotime($request->created_at)) ? date('d-m-Y h:i', strtotime($request->created_at)) : '--' }}
                            </td>
                            <td>
                                 {{ucwords($request->paymentMode)}}

                            </td>
    
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $request->paymentStatus }}</div>
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($totalRecords > 0)
        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
    @endif
    <div class="d-inline addbtn intro-y col-span-12">
        <nav class="w-full sm:w-auto sm:mr-auto">
            <ul class="pagination" id="pagination">
                <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('walletHistory', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-left"></i>
                    </a>
                </li>
    
                @php
                    $showPages = 15; // Number of pages to show at a time
                    $halfShowPages = floor($showPages / 2);
                    $startPage = max(1, $page - $halfShowPages);
                    $endPage = min($startPage + $showPages - 1, $totalPages);
                @endphp
    
                @if ($startPage > 1)
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ route('walletHistory', ['page' => 1, 'searchString' => $searchString]) }}">1</a>
                    </li>
                    @if ($startPage > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif
    
                @for ($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $page == $i ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ route('walletHistory', ['page' => $i, 'searchString' => $searchString]) }}">{{ $i }}</a>
                    </li>
                @endfor
    
                @if ($endPage < $totalPages)
                    @if ($endPage < $totalPages - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ route('walletHistory', ['page' => $totalPages, 'searchString' => $searchString]) }}">{{ $totalPages }}</a>
                    </li>
                @endif
    
                <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link"
                        href="{{ route('walletHistory', ['page' => $page + 1, 'searchString' => $searchString]) }}">
                        <i class="w-4 h-4" data-lucide="chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    
    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i class="w-16 h-16 text-danger mx-auto mt-3"></i>

                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 py-4">Do you really want to Release This Amount?
                        </div>

                        <form action="{{ route('releaseAmount') }} " method="POST">
                            @csrf
                            <input type="hidden" id="del_id" name="del_id">
                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal"
                                    class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                <button data-tw-dismiss="modal" class="btn btn-primary w-24">Release</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script type="text/javascript">
            function delbtn($id, $name) {
                var id = $id;
                $did = id;

                $('#del_id').val($did);
                $('#id').val($id);
            }
        </script>
        <script>
            $(window).on('load', function() {
                $('.loader').hide();
            })
        </script>
    @endsection
