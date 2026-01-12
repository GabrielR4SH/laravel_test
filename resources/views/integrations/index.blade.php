<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Jobs</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Last 20 Integration Jobs</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>External ID</th>
                <th>Status</th>
                <th>Last Error</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
            <tr>
                <td>{{ $job->id }}</td>
                <td>{{ $job->external_id }}</td>
                <td>{{ $job->status }}</td>
                <td>{{ $job->last_error }}</td>
                <td>{{ $job->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
