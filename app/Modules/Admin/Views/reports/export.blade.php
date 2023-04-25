<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Namn</th>
        <th>E-post</th>
        <th>Datum</th>
    </tr>
    </thead>
    <tbody>
    @foreach($students as $student)
        <tr>
            <td>{{ $student->id }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
