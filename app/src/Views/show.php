<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Projekt #<?= $project->id ?></h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <?php if (!empty($project)) : ?>
            <div class="bg-white rounded-lg shadow px-5 py-4 grid gap-4">
                <div class="grid grid-cols-2 items-center">
                    <h2 class="text-lg font-semibold">
                        <?= $project->title ?>
                    </h2>
                    <div class="text-right">
                        <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded <?= $project->statusKey === 'todo' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : ($project->statusKey === 'in_progress' ? 'bg-blue-100 text-blue-800 border border-blue-400' : 'bg-green-100 text-green-800 border border-green-400') ?>">
                            <?= $project->statusName ?>
                        </span>
                    </div>
                </div>
                <div>
                    <p><?= $project->description ?></p>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex space-x-2">
                        <a href="/edit?id=<?= $project->id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-edit"></i></a>
                        <a href="/delete?id=<?= $project->id ?>" class="delete-project bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-trash-alt"></i></a>
                        <a href="/" class="bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-list"></i></a>
                    </div>
                    <p class="text-sm text-gray-500"><?= $project->ownerName ?> (<?= $project->ownerEmail ?>)</p>
                </div>
            </div>
        <?php else : ?>
            <div class="bg-gray-200 border border-gray-300 p-1 px-2 rounded">A projekt nem található.</div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelector('.delete-project').addEventListener('click', function(event) {
            event.preventDefault();
            var deleteUrl = this.getAttribute('href');

            Swal.fire({
                title: 'Biztos vagy benne, hogy törölni akarod a projektet?',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: 'Igen',
                confirmButtonColor: '#dd6b55',
                denyButtonText: 'Nem',
                cancelButtonText: 'Mégse',
                focusCancel: true,
                icon: 'warning',
                customClass: {
                    cancelButton: 'order-1 right-gap',
                    confirmButton: 'order-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(deleteUrl, {
                            method: 'GET',
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(data.message, '', 'success')
                                    .then((value) => {
                                        window.location.href = '/';
                                    });
                            } else {
                                Swal.fire(data.message, '', 'error');
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            toastr.error('An error occurred', event);
                        });
                }
            })
        });
    });
</script>