<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'intervention - Ticket #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'intervention</h1>
        <h2>Ticket #{{ $ticket->id }}</h2>
        <p>Date de génération : {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informations générales</div>
        <table>
            <tr>
                <th>Équipement</th>
                <td>{{ $equipement->designation }} - {{ $equipement->modele }} - {{ $equipement->marque }}</td>
            </tr>
            <tr>
                <th>Type de ticket</th>
                <td>{{ ucfirst($ticket->type_ticket) }}</td>
            </tr>
            <tr>
                <th>Priorité</th>
                <td>{{ ucfirst($ticket->priorite) }}</td>
            </tr>
            <tr>
                <th>Statut</th>
                <td>{{ ucfirst($ticket->statut) }}</td>
            </tr>
            <tr>
                <th>Gravité de la panne</th>
                <td>{{ ucfirst($ticket->gravite_panne) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Description du problème</div>
        <p>{{ $ticket->description }}</p>
    </div>

    <div class="section">
        <div class="section-title">Intervention</div>
        <table>
            <tr>
                <th>Date d'intervention</th>
                <td>{{ $ticket->date_intervention?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Date de résolution</th>
                <td>{{ $ticket->date_resolution?->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Temps d'arrêt</th>
                <td>{{ $ticket->temps_arret }}</td>
            </tr>
            <tr>
                <th>Type d'intervention</th>
                <td>{{ $ticket->type_externe ? 'Externe' : 'Interne' }}</td>
            </tr>
            @if($ticket->type_externe)
            <tr>
                <th>Fournisseur</th>
                <td>{{ $ticket->fournisseur }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Diagnostic et Solution</div>
        <h3>Diagnostic</h3>
        <p>{{ $ticket->diagnostic }}</p>
        <h3>Solution</h3>
        <p>{{ $ticket->solution }}</p>
    </div>

    @if($ticket->pieces->isNotEmpty())
    <div class="section">
        <div class="section-title">Pièces utilisées</div>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Référence</th>
                    <th>Quantité utilisée</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticket->pieces as $piece)
                <tr>
                    <td>{{ $piece->designation }}</td>
                    <td>{{ $piece->reference }}</td>
                    <td>{{ $piece->pivot->quantite_utilisee }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Personnel impliqué</div>
        <table>
            <tr>
                <th>Créé par</th>
                <td>{{ $createur->name }} {{ $createur->prenom }}</td>
            </tr>
            @if($assignee)
            <tr>
                <th>Assigné à</th>
                <td>{{ $assignee->name }} {{ $assignee->prenom }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système GMAO</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html> 