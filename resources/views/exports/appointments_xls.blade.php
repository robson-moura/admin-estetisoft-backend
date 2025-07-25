<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Profissional</th>
            <th>Serviço</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Forma de Pagamento</th>
            <th>Plano</th>
            <th>Assinatura</th>
            <th>Observações</th>
            <th>Produtos Utilizados</th>
            <th>Foto Antes</th>
            <th>Foto Depois</th>
            <th>Criado em</th>
            <th>Atualizado em</th>
        </tr>
    </thead>
    <tbody>
        @foreach($appointments as $a)
            <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->client->full_name ?? '' }}</td>
                <td>{{ $a->user->name ?? '' }}</td>
                <td>{{ $a->service->name ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</td>
                <td>{{ $a->time }}</td>
                <td>{{ $a->status }}</td>
                <td>{{ $a->price }}</td>
                <td>{{ $a->payment_method }}</td>
                <td>{{ $a->plan }}</td>
                <td>{{ $a->signature }}</td>
                <td>{{ $a->notes }}</td>
                <td>
                    @if($a->products && count($a->products))
                        {{ $a->products->pluck('name')->join(', ') }}
                    @endif
                </td>
                <td>
                    @if($a->before_photo)
                        <a href="{{ url($a->before_photo) }}">Ver Foto</a>
                    @endif
                </td>
                <td>
                    @if($a->after_photo)
                        <a href="{{ url($a->after_photo) }}">Ver Foto</a>
                    @endif
                </td>
                <td>{{ $a->created_at ? $a->created_at->format('d/m/Y H:i') : '' }}</td>
                <td>{{ $a->updated_at ? $a->updated_at->format('d/m/Y H:i') : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>