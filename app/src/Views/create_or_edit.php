<?php
if (isset($project)) {
    $title = "Projekt szerkesztése";
    $action = "/update?id=" . $project->id;
    $projectTitle = isset($formData["projekt_cim"]) ? $formData["projekt_cim"] : $project->title;
    $projectDescription = isset($formData["projekt_leiras"]) ? $formData["projekt_leiras"] : $project->description;
    $projectStatus = isset($formData["projekt_statusz"]) ? $formData["projekt_statusz"] : $project->statusId;
    $projectOwner = isset($formData["projekt_owner"]) ? $formData["projekt_owner"] : $project->ownerId;
    $projectOwnerName = isset($formData["projekt_kapcsolattarto_neve"]) ? $formData["projekt_kapcsolattarto_neve"] : $project->ownerName;
    $projectOwnerEmail = isset($formData["projekt_kapcsolattarto_email"]) ? $formData["projekt_kapcsolattarto_email"] : $project->ownerEmail;
} else {
    $title = "Új projekt létrehozása";
    $action = "/store";
    $projectTitle = isset($formData["projekt_cim"]) ? $formData["projekt_cim"] : "";
    $projectDescription = isset($formData["projekt_leiras"]) ? $formData["projekt_leiras"] : "";
    $projectStatus = isset($formData["projekt_statusz"]) ? $formData["projekt_statusz"] : "none";
    $projectOwner = isset($formData["projekt_owner"]) ? $formData["projekt_owner"] : "none";
    $projectOwnerName = isset($formData["projekt_kapcsolattarto_neve"]) ? $formData["projekt_kapcsolattarto_neve"] : "";
    $projectOwnerEmail = isset($formData["projekt_kapcsolattarto_email"]) ? $formData["projekt_kapcsolattarto_email"] : "";
}
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $title ?></h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <?php if (isset($successMessage)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Siker!</strong>
                <span class="block"><?= $successMessage ?></span>
            </div>
            <meta http-equiv='refresh' content='4;url=/' />
        <?php endif; ?>
        <?php if (isset($errors)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Hoppá!</strong>
                <span class="block"><?= implode("<br>", $errors) ?></span>
            </div>
        <?php endif; ?>
        <form id="projekt_mentes_form" action="<?= $action ?>" method="post" class="space-y-4">
            <div>
                <label for="cim" class="block text-sm font-medium text-gray-700 mb-2">Cím</label>
                <input type="text" id="cim" name="projekt_cim" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="<?= $projectTitle ?>">
            </div>
            <div>
                <label for="leiras" class="block text-sm font-medium text-gray-700 mb-2">Leírás</label>
                <textarea id="leiras" name="projekt_leiras" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?= $projectDescription ?></textarea>
            </div>
            <div class="flex flex-col">
                <label for="statusz" class="mr-2 block text-sm font-medium text-gray-700 mb-2">Státusz</label>
                <div class="inline-block">
                    <select id="statusz" name="projekt_statusz" class="w-auto bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3">
                        <option value="none" <?= $projectStatus === "none" || $projectStatus === "" ? "selected" : "" ?>>Válassz státuszt</option>
                        <?php if (isset($statuses)) : ?>
                            <?php foreach ($statuses as $status) : ?>
                                <option value="<?= $status->id ?>" <?= $status->id === (int)$projectStatus ? "selected" : "" ?>><?= $status->name ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="owner" class="mb-2 block text-sm font-medium text-gray-700">Kapcsolattartó</label>
                <select id="owner" name="projekt_owner" class="w-auto bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3" onchange="checkNewOwner(this)">
                    <option value="none" <?= $projectOwner === "none" || $projectOwner === "" ? "selected" : "" ?>>Válassz kapcsolattartót</option>
                    <option value="new" <?= $projectOwner === "new" ? "selected" : "" ?>>+ Új kapcsolattartó felvétele</option>
                    <?php if (isset($owners)) : ?>
                        <?php foreach ($owners as $owner) : ?>
                            <option value="<?= $owner->id ?>" <?= $owner->id === (int)$projectOwner ? "selected" : "" ?>><?= $owner->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div id="new_owner_fields" class="hidden flex flex-col mt-4">
                <label for="projekt_kapcsolattarto_neve" class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="kapcsolattarto_neve" name="projekt_kapcsolattarto_neve" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mb-2" value="<?= $projectOwnerName ?>">

                <label for="kapcsolattarto_email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="kapcsolattarto_email" name="projekt_kapcsolattarto_email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="<?= $projectOwnerEmail ?>">
            </div>
            <button type="submit" id="projekt_mentes" class="bg-gray-200 hover:bg-gray-300 text-gray-600 inline-flex items-center px-4 py-2 font-bold rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 <?php if (isset($successMessage)) echo 'opacity-50 pointer-events-none'; ?>">
                Mentés
            </button>
        </form>
    </div>
</main>

<script>
    function checkNewOwner(selectElement) {
        var newOwnerForm = document.getElementById('new_owner_fields');
        if (selectElement.value === 'new') {
            newOwnerForm.classList.remove('hidden');
        } else {
            newOwnerForm.classList.add('hidden');
            document.getElementById('kapcsolattarto_neve').value = '';
            document.getElementById('kapcsolattarto_email').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var selectElement = document.getElementById('owner');
        checkNewOwner(selectElement);
    });
</script>