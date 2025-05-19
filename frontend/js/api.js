// Funzione per ottenere tutti gli items
async function getItems() {
    try {
        const response = await fetch('http://localhost/bitems/api/items.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        if (data.success) {
            return data.data;
        } else {
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Error fetching items:', error);
        throw error;
    }
}

// Funzione per creare un nuovo item
async function createItem(itemData) {
    try {
        const response = await fetch('http://localhost/bitems/api/items.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(itemData)
        });
        
        const data = await response.json();
        if (data.success) {
            return data.message;
        } else {
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Error creating item:', error);
        throw error;
    }
}

// Esempio di utilizzo:
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Ottieni tutti gli items
        const items = await getItems();
        console.log('Items:', items);
        
        // Crea un nuovo item
        const newItem = {
            name: 'Nuovo Item',
            description: 'Descrizione del nuovo item'
        };
        const result = await createItem(newItem);
        console.log('Create result:', result);
    } catch (error) {
        console.error('Error:', error);
    }
}); 