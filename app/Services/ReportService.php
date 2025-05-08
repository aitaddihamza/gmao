<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function generateTicketReport($ticket, $equipement, $createur, $assignee)
    {
        // Supprimer l'ancien rapport s'il existe
        if ($ticket->rapport_path) {
            $oldPath = $ticket->rapport_path;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $phpWord = new PhpWord();
        
        // Styles
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16], ['spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14], ['spaceAfter' => 120]);
        
        $section = $phpWord->addSection();
        
        // En-tête
        $section->addTitle('Rapport d\'intervention', 1);
        $section->addText('Ticket #' . $ticket->id, ['bold' => true, 'size' => 12]);
        $section->addText('Date de génération : ' . now()->format('d/m/Y H:i'), ['size' => 10]);
        $section->addTextBreak(2);
        
        // Informations générales
        $section->addTitle('Informations générales', 2);
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table->addRow();
        $table->addCell()->addText('Équipement', ['bold' => true]);
        $table->addCell()->addText($equipement->designation . ' - ' . $equipement->modele . ' - ' . $equipement->marque);
        
        $table->addRow();
        $table->addCell()->addText('Type de ticket', ['bold' => true]);
        $table->addCell()->addText(ucfirst($ticket->type_ticket));
        
        $table->addRow();
        $table->addCell()->addText('Priorité', ['bold' => true]);
        $table->addCell()->addText(ucfirst($ticket->priorite));
        
        $table->addRow();
        $table->addCell()->addText('Statut', ['bold' => true]);
        $table->addCell()->addText(ucfirst($ticket->statut));
        
        if ($ticket->type_ticket === 'correctif') {
            $table->addRow();
            $table->addCell()->addText('Gravité de la panne', ['bold' => true]);
            $table->addCell()->addText(ucfirst($ticket->gravite_panne));
        }
        
        $section->addTextBreak(2);
        
        // Description du problème
        $section->addTitle('Description du problème', 2);
        $section->addText($ticket->description);
        $section->addTextBreak(2);
        
        // Intervention
        $section->addTitle('Intervention', 2);
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        
        $table->addRow();
        $table->addCell()->addText('Date d\'intervention', ['bold' => true]);
        $table->addCell()->addText($ticket->date_intervention?->format('d/m/Y H:i'));
        
        $table->addRow();
        $table->addCell()->addText('Date de résolution', ['bold' => true]);
        $table->addCell()->addText($ticket->date_resolution?->format('d/m/Y H:i'));
        
        $table->addRow();
        $table->addCell()->addText('Temps d\'arrêt', ['bold' => true]);
        $table->addCell()->addText($ticket->temps_arret);
        
        $table->addRow();
        $table->addCell()->addText('Type d\'intervention', ['bold' => true]);
        $table->addCell()->addText($ticket->type_externe ? 'Externe' : 'Interne');
        
        if ($ticket->type_externe) {
            $table->addRow();
            $table->addCell()->addText('Fournisseur', ['bold' => true]);
            $table->addCell()->addText($ticket->fournisseur);
        }
        
        $section->addTextBreak(2);
        
        // Diagnostic et Solution
        if ($ticket->type_ticket === 'correctif') {
            $section->addTitle('Diagnostic et Solution', 2);
            $section->addText('Diagnostic:', ['bold' => true]);
            $section->addText($ticket->diagnostic);
            $section->addTextBreak();
            $section->addText('Solution:', ['bold' => true]);
            $section->addText($ticket->solution);
            $section->addTextBreak(2);
        }
        
        // Pièces utilisées
        if ($ticket->pieces->isNotEmpty()) {
            $section->addTitle('Pièces utilisées', 2);
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            
            // En-tête du tableau
            $table->addRow();
            $table->addCell()->addText('Désignation', ['bold' => true]);
            $table->addCell()->addText('Référence', ['bold' => true]);
            $table->addCell()->addText('Quantité utilisée', ['bold' => true]);
            $table->addCell()->addText('Prix unitaire', ['bold' => true]);
            $table->addCell()->addText('Total', ['bold' => true]);
            
            $totalGeneral = 0;
            foreach ($ticket->pieces as $piece) {
                $table->addRow();
                $table->addCell()->addText($piece->designation);
                $table->addCell()->addText($piece->reference);
                $table->addCell()->addText($piece->pivot->quantite_utilisee);
                $table->addCell()->addText(number_format($piece->prix_unitaire, 2) . ' €');
                $total = $piece->prix_unitaire * $piece->pivot->quantite_utilisee;
                $totalGeneral += $total;
                $table->addCell()->addText(number_format($total, 2) . ' €');
            }
            
            // Ligne du total
            $table->addRow();
            $table->addCell(null, ['gridSpan' => 4])->addText('Total général', ['bold' => true]);
            $table->addCell()->addText(number_format($totalGeneral, 2) . ' €', ['bold' => true]);
            
            $section->addTextBreak(2);
        }
        
        // Personnel impliqué
        $section->addTitle('Personnel impliqué', 2);
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        
        $table->addRow();
        $table->addCell()->addText('Créé par', ['bold' => true]);
        $table->addCell()->addText($createur->name . ' ' . $createur->prenom);
        
        if ($assignee) {
            $table->addRow();
            $table->addCell()->addText('Assigné à', ['bold' => true]);
            $table->addCell()->addText($assignee->name . ' ' . $assignee->prenom);
        }
        
        // Pied de page
        $section->addTextBreak(2);
        $section->addText('Ce rapport a été généré automatiquement par le système GMAO', ['size' => 8, 'italic' => true]);
        $section->addText('© ' . date('Y') . ' - Tous droits réservés', ['size' => 8, 'italic' => true]);
        
        // Sauvegarde du document
        $filename = 'rapport_ticket_' . $ticket->id . '_' . now()->format('Y-m-d_His') . '.docx';
        $path = 'reports/' . $filename;
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path('app/public/' . $path));
        
        return $path;
    }

    public function generateMaintenancePreventiveReport($maintenance, $equipement)
    {
        try {
            // Supprimer l'ancien rapport s'il existe
            if ($maintenance->rapport_path && Storage::disk('public')->exists($maintenance->rapport_path)) {
                Storage::disk('public')->delete($maintenance->rapport_path);
            }

            // Créer le dossier s'il n'existe pas
            $directory = 'reports/maintenance';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $phpWord = new PhpWord();

            // Styles
            $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16], ['spaceAfter' => 240]);
            $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14], ['spaceAfter' => 120]);

            $section = $phpWord->addSection();

            // En-tête
            $section->addTitle('Rapport de maintenance préventive', 1);
            $section->addText('Maintenance #' . $maintenance->id, ['bold' => true, 'size' => 12]);
            $section->addText('Date de génération : ' . now()->format('d/m/Y H:i'), ['size' => 10]);
            $section->addTextBreak(2);

            // Informations générales
            $section->addTitle('Informations générales', 2);
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

            $table->addRow();
            $table->addCell()->addText('Équipement', ['bold' => true]);
            $table->addCell()->addText($equipement->designation . ' - ' . $equipement->modele . ' - ' . $equipement->marque);

            $table->addRow();
            $table->addCell()->addText('Statut', ['bold' => true]);
            $table->addCell()->addText(ucfirst($maintenance->statut));

            $table->addRow();
            $table->addCell()->addText('Type de maintenance', ['bold' => true]);
            $table->addCell()->addText($maintenance->type_externe ? 'Externe' : 'Interne');

            $table->addRow();
            $table->addCell()->addText('Date début', ['bold' => true]);
            $table->addCell()->addText($maintenance->date_debut?->format('d/m/Y H:i') ?? '-');

            $table->addRow();
            $table->addCell()->addText('Date fin', ['bold' => true]);
            $table->addCell()->addText($maintenance->date_fin?->format('d/m/Y H:i') ?? '-');

            if ($maintenance->type_externe) {
                $table->addRow();
                $table->addCell()->addText('Fournisseur', ['bold' => true]);
                $table->addCell()->addText($maintenance->fournisseur);
            }

            $section->addTextBreak(2);

            // Description
            $section->addTitle('Description', 2);
            $section->addText($maintenance->description);
            $section->addTextBreak(2);

            // Actions réalisées
            if ($maintenance->actions_realisees) {
                $section->addTitle('Actions réalisées', 2);
                $section->addText($maintenance->actions_realisees);
                $section->addTextBreak(2);
            }

            // Pièces utilisées
            if ($maintenance->pieces->isNotEmpty()) {
                $section->addTitle('Pièces utilisées', 2);
                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

                // En-tête du tableau
                $table->addRow();
                $table->addCell()->addText('Désignation', ['bold' => true]);
                $table->addCell()->addText('Référence', ['bold' => true]);
                $table->addCell()->addText('Quantité utilisée', ['bold' => true]);
                $table->addCell()->addText('Prix unitaire', ['bold' => true]);
                $table->addCell()->addText('Total', ['bold' => true]);

                $totalGeneral = 0;
                foreach ($maintenance->pieces as $piece) {
                    $table->addRow();
                    $table->addCell()->addText($piece->designation);
                    $table->addCell()->addText($piece->reference);
                    $table->addCell()->addText($piece->pivot->quantite_utilisee);
                    $table->addCell()->addText(number_format($piece->prix_unitaire, 2) . ' €');
                    $total = $piece->prix_unitaire * $piece->pivot->quantite_utilisee;
                    $totalGeneral += $total;
                    $table->addCell()->addText(number_format($total, 2) . ' €');
                }

                // Ligne du total
                $table->addRow();
                $table->addCell(null, ['gridSpan' => 4])->addText('Total général', ['bold' => true]);
                $table->addCell()->addText(number_format($totalGeneral, 2) . ' €', ['bold' => true]);

                $section->addTextBreak(2);
            }

            // Assignation
            $section->addTitle('Assignation', 2);
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);

            $table->addRow();
            $table->addCell()->addText('Créé par', ['bold' => true]);
            $table->addCell()->addText($maintenance->createur?->name . ' ' . $maintenance->createur?->prenom);

            if ($maintenance->assignee) {
                $table->addRow();
                $table->addCell()->addText('Assigné à', ['bold' => true]);
                $table->addCell()->addText($maintenance->assignee?->name . ' ' . $maintenance->assignee?->prenom);
            }

            $section->addTextBreak(2);

            // Pied de page
            $section->addText('Ce rapport a été généré automatiquement par le système GMAO', ['size' => 8, 'italic' => true]);
            $section->addText('© ' . date('Y') . ' - Tous droits réservés', ['size' => 8, 'italic' => true]);

            // Sauvegarde du document
            $filename = 'rapport_maintenance_' . $maintenance->id . '_' . now()->format('Y-m-d_His') . '.docx';
            $path = $directory . '/' . $filename;

            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);

            // Copier le fichier temporaire vers le stockage
            Storage::disk('public')->put($path, file_get_contents($tempFile));

            // Nettoyer le fichier temporaire
            @unlink($tempFile);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du rapport : ' . $e->getMessage());
            throw $e;
        }
    }
}