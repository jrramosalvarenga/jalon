function initContactImport() {
    const button = document.getElementById('import-phone-contacts');
    const resultsContainer = document.getElementById('phone-contacts-results');

    if (!button || !resultsContainer) {
        return;
    }

    if (!('contacts' in navigator) || !('ContactsManager' in window)) {
        button.classList.add('hidden');
        return;
    }

    button.classList.remove('hidden');

    button.addEventListener('click', async () => {
        try {
            const contacts = await navigator.contacts.select(['name', 'email', 'tel'], { multiple: true });

            const emails = [];
            const phones = [];

            contacts.forEach((contact) => {
                (contact.email || []).forEach((email) => emails.push(email));
                (contact.tel || []).forEach((tel) => phones.push(tel));
            });

            const response = await fetch('/contacts/match-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ emails, phones }),
            });

            const data = await response.json();
            renderMatches(data.matches || []);
        } catch (error) {
            console.error('Contact import failed:', error);
        }
    });

    function renderMatches(matches) {
        resultsContainer.innerHTML = '';

        if (matches.length === 0) {
            resultsContainer.innerHTML = '<p class="text-sm text-gray-500">No se encontraron contactos de Jalon en tu teléfono.</p>';
            return;
        }

        matches.forEach((match) => {
            const row = document.createElement('div');
            row.className = 'border border-gray-100 bg-gray-50 rounded-2xl p-4 flex items-center justify-between';

            const info = document.createElement('div');
            info.className = 'text-sm text-gray-900';
            info.innerHTML = `<div class="font-semibold">${match.name}</div><div class="text-gray-500">${match.email}</div>`;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/contacts/request/${match.id}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').content;

            const submit = document.createElement('button');
            submit.type = 'submit';
            submit.className = 'inline-flex items-center justify-center px-6 py-3 bg-black border border-transparent rounded-full font-semibold text-sm text-white hover:bg-gray-800';
            submit.textContent = 'Enviar solicitud';

            form.appendChild(csrf);
            form.appendChild(submit);

            row.appendChild(info);
            row.appendChild(form);
            resultsContainer.appendChild(row);
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContactImport);
} else {
    initContactImport();
}
