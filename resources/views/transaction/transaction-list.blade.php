@extends('layouts')
@section('title', 'All transaction list')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Current balance transaction list</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                        <li class="breadcrumb-item active">Transaction</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaction list</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="alert alert-success">
                                <b>Current balance: </b>{{ number_format($data->balance, 2) }}
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Fee</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data->transaction as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->transaction_type }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->fee }}</td>
                                            <td>{{ $item->date->format('d-m-Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="5">No transaction happend.</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->

                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
@endsection
