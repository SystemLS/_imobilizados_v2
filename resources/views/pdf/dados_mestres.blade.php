@extends('pdf.template')

@section('conteudo')
    <h1>{{ $titulo ?? 'Relatório de Dados Mestres' }}</h1>

    <div class="info">
        <p>{{ $descricao ?? 'Relatório gerado a partir dos dados mestres.' }}</p>
    </div>

    @if(empty($rows))
        <p style="text-align: center; color: #999; padding: 20px;">Nenhum registro disponível para exportação.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
