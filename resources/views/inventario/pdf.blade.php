<!DOCTYPE html>
<html>
<head>
    <title>Inventário de Ativos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Inventário de Ativos</h2>
    <table>
        <thead>
            <tr>
                <th>Etiqueta</th>
                <th>Nome</th>
                <th>Grupo</th>
                <th>Categoria</th>
                <th>Localização</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bens as $bem)
            <tr>
                <td>{{ $bem->Etiqueta ?? '-' }}</td>
                <td>{{ $bem->Nome }}</td>
                <td>{{ $bem->grupo->Nome ?? '-' }}</td>
                <td>{{ $bem->categoria->Nome ?? '-' }}</td>
                <td>{{ optional($bem->sala->piso->edificio->provincia)->Nome ?? '-' }} / {{ optional($bem->sala->piso->edificio)->Nome ?? '-' }} / {{ optional($bem->sala->piso)->Nome ?? '-' }} / {{ $bem->sala->Nome ?? '-' }}</td>
                <td>{{ $bem->estadoConservacao->Nome ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
