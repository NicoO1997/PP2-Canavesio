function updateMachinery(machineryId) {
    const form = document.querySelector(`#machinery-${machineryId}`);
    const formData = new FormData();

    // Extraer los datos del formulario manualmente
    formData.append('machineryName', form.querySelector('[name="machineryName"]').value);
    formData.append('brand', form.querySelector('[name="brand"]').value);
    formData.append('yearsOld', form.querySelector('[name="yearsOld"]').value);
    formData.append('months', form.querySelector('[name="months"]').value);
    formData.append('hoursOfUse', form.querySelector('[name="hoursOfUse"]').value);
    formData.append('lastService', form.querySelector('[name="lastService"]').value);
    formData.append('price', form.querySelector('[name="price"]').value);
    const image = form.querySelector('[name="image"]').files[0];
    if (image) {
        formData.append('image', image);
    }

    fetch(`/admin/used-machinery/update/${machineryId}`, {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update machinery');
        }
        return response.text(); // O JSON si la respuesta es JSON
    })
    .then(data => {
        alert('Machinery updated successfully!');
        window.location.reload(); // Recargar la página automáticamente
    })
    .catch(error => {
        console.error('Error updating machinery:', error);
        alert('There was an error updating the machinery.');
    });
}