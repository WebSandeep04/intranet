<table class="table table-bordered">
    <thead>
        <tr>
            <th>Prospectus Name</th>
            <th>Status ID</th>
            <th>Product Type ID</th>
            <th>Lead Source ID</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $sale)
            <tr>
                <td>{{ $sale->prospectus_name }}</td>
                <td>{{ $sale->status_id }}</td>
                <td>{{ $sale->products_id }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
