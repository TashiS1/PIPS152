document.addEventListener('DOMContentLoaded', function() {
    // Event listener for the delete buttons
    document.querySelectorAll('.delete-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément?')) {
                var form = button.closest('form');
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                }).then(function(response) {
                    if (response.ok) {
                        // Remove the row from the table
                        var row = button.closest('tr');
                        row.parentNode.removeChild(row);
                    } else {
                        alert('Une erreur s\'est produite lors de la suppression.');
                    }
                }).catch(function(error) {
                    alert('Une erreur s\'est produite : ' + error);
                });
            }
        });
    });

    // Event listener for the material selection to check status
    document.getElementById('materiel').addEventListener('change', function() {
        var materielRef = this.value;
        fetch('{{ path('check_materiel_status') }}?reference=' + materielRef)
            .then(response => response.json())
            .then(data => {
                var statusField = document.getElementById('status');
                var permanentField = document.getElementById('permanent');
                
                if (data.status === 'En utilisation') {
                    statusField.value = 'affecté';
                    statusField.disabled = true;

                    permanentField.value = data.permanentId;
                    permanentField.disabled = true;
                } else {
                    statusField.value = '';
                    statusField.disabled = false;

                    permanentField.value = '';
                    permanentField.disabled = false;
                }
            });
    });
});
