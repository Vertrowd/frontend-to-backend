function submitForm() {
    const formData = new FormData(form);
    
    // Log FormData contents for debugging
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Uploading...';

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showMessage(data.message, 'success');
            form.reset();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showMessage('An error occurred while uploading. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Upload File';
    });
}