<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Log Name</th>
        <th>Description</th>
        <th>User</th>
        <th>IP</th>
        <th>Browser</th>
        <th>OS</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    {{ $no = 1; }}
    @foreach($activity_logs as $activity_log)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $activity_log->log_name }}</td>
            <td>{{ $activity_log->description }}</td>
            <td>{{ $activity_log->user }}</td>
            <td>{{ $activity_log->ip }}</td>
            <td>{{ $activity_log->browser }}</td>
            <td>{{ $activity_log->os }}</td>
            <td>{{ $activity_log->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>