<x-layouts.admin>

<div class="py-12">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- En-tête -->

        <div class="flex justify-between items-center mb-6">

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    💼 Détails de l'abonnement
                </h1>

                <p class="text-gray-500 mt-2">
                    Informations détaillées de l'abonnement.
                </p>

            </div>

            <a
                href="{{ route('admin.abonnements') }}"
                class="bg-slate-600 hover:bg-slate-700 text-white px-5 py-3 rounded-xl font-semibold"
            >
                ← Retour
            </a>

        </div>


        <!-- Carte abonnement -->

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            <!-- Agence -->

            <div class="p-6 border-b">

                <p class="text-sm text-gray-500 mb-1">
                    🏢 Agence
                </p>

                <p class="text-xl font-bold text-slate-800">
                    {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}
                </p>

            </div>


            <!-- Informations -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">

                <!-- Type -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        📦 Type d'abonnement
                    </p>

                    <p class="text-lg font-semibold text-slate-800">
                        {{ $abonnement->type }}
                    </p>

                </div>


                <!-- Montant -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        💰 Montant
                    </p>

                    <p class="text-lg font-bold text-green-600">
                        {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                    </p>

                </div>


                <!-- Date début -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        📅 Date de début
                    </p>

                    <p class="text-lg font-semibold text-slate-800">

                        {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}

                    </p>

                </div>


                <!-- Date fin -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        📅 Date de fin
                    </p>

                    <p class="text-lg font-semibold text-slate-800">

                        {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}

                    </p>

                </div>


                <!-- Statut -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        📌 Statut
                    </p>

                    @if($abonnement->statut === 'Actif')

                        <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold">
                            ✅ Actif
                        </span>

                    @else

                        <span class="inline-block bg-red-100 text-red-700 px-4 py-2 rounded-full font-semibold">
                            ❌ Expiré
                        </span>

                    @endif

                </div>


                <!-- ID -->

                <div class="bg-slate-50 rounded-xl p-5">

                    <p class="text-sm text-gray-500 mb-2">
                        🔢 Identifiant
                    </p>

                    <p class="text-lg font-semibold text-slate-800">
                        #{{ $abonnement->id }}
                    </p>

                </div>

            </div>


            <!-- Paiement agence -->

            @if($abonnement->paiementAgences && $abonnement->paiementAgences->count() > 0)

                <div class="border-t p-6">

                    <h2 class="text-xl font-bold text-slate-800 mb-4">
                        💳 Paiement de l'agence
                    </h2>

                    @foreach($abonnement->paiementAgences as $paiement)

                        <div class="bg-slate-50 rounded-xl p-5 mb-3">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>

                                    <p class="text-sm text-gray-500">
                                        Montant payé
                                    </p>

                                    <p class="font-bold text-green-600">
                                        {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                    </p>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500">
                                        Mode de paiement
                                    </p>

                                    <p class="font-semibold text-slate-800">
                                        {{ $paiement->mode_paiement ?? 'Non renseigné' }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500">
                                        Date du paiement
                                    </p>

                                    <p class="font-semibold text-slate-800">
                                        {{ $paiement->date_paiement
                                            ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')
                                            : 'Non renseignée'
                                        }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500">
                                        Statut du paiement
                                    </p>

                                    <p class="font-semibold text-slate-800">
                                        {{ $paiement->statut ?? 'Non renseigné' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif


            <!-- Actions -->

            <div class="border-t p-6 flex flex-col sm:flex-row gap-4">

                <!-- Modifier -->

                <a
                    href="{{ route('admin.abonnements.edit', $abonnement->id) }}"
                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-xl font-bold"
                >
                    ✏️ Modifier l'abonnement
                </a>


                <!-- Retour -->

                <a
                    href="{{ route('admin.abonnements') }}"
                    class="flex-1 bg-slate-600 hover:bg-slate-700 text-white text-center px-6 py-3 rounded-xl font-bold"
                >
                    ← Retour aux abonnements
                </a>

            </div>

        </div>

    </div>

</div>

</x-layouts.admin>