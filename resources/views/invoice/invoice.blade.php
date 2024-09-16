
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Facture #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 40px; }
    </style>
</head>
<body>
<div class="container header m-4">
    <div class="container d-flex justify-content-between align-items">
        <div class="header">
            <img src="{{asset('logo/logo.png')}}" width="120px" height="120px">
        </div>
        <div class="header">
            <h1>Facture #{{ $order->id }}</h1>
            <p>Date: {{ $order->paid_at }}</p>
        </div>
    </div>
    <div class="row p-5">
        <div class="container">
            <div class="row w-75 header m-auto">
                <div class="col-md-6">
                    <h2>De :</h2>
                    <p><strong> Nom : {{$order->demande->annonce->user->name}}</strong></p>
                    <p>Adresse : {{$order->demande->annonce->user->profile->address}}</p>
                    <p>Email : {{$order->demande->annonce->user->email}}</p>
                    <p>Téléphone : 123-456-7890</p>
                </div>
                <div class="col-md-6">
                    <h2>À :</h2>
                    <p><strong>Nom : {{$order->demande->user->name}}</strong></p>
                    <p>Adresse : {{$order->demande->user->profile->address}}</p>
                    <p>Email : {{$order->demande->user->email}}</p>
                    <p>Téléphone : 123-456-7890</p>
                </div>
            </div>
        </div>

    </div>
</div>


<table>
    <thead class="table-success">
    <tr>
        <th>Description</th>
        <th>Quantité(Kg)</th>
        <th>Prix unitaire(MAD)</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
{{--    @dump($order->demande->annonce)--}}
        <tr>
            <td>{{$order->demande->annonce->description}}</td>
            <td>{{$order->demande->kilos_demandes}}</td>
            <td>{{$order->demande->annonce->prix_du_kilo}}</td>
            <td>{{$order->total}}</td>
        </tr>
    <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td>{{ $order->total }} MAD</td>
    </tr>
    </tbody>
</table>
</body>
</html>
