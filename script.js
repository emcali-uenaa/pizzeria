document.addEventListener("DOMContentLoaded", () => {
    const menuContainer = document.getElementById("menu-container");

    // Realiza una solicitud a la API
    fetch("api.php")
        .then(response => response.json())
        .then(data => {
            const categories = {};

            // Agrupa los elementos del menú por categoría
            data.forEach(item => {
                if (!categories[item.category]) {
                    categories[item.category] = [];
                }
                categories[item.category].push(item);
            });

            // Crea secciones dinámicamente para cada categoría
            for (const [category, items] of Object.entries(categories)) {
                const section = document.createElement("section");
                section.id = category.toLowerCase();
                section.innerHTML = `
                    <h2>${category}</h2>
                    <div class="menu-items">
                        ${items.map(item => {
                            const imageUrl = `uploads/${item.image}`; // Verifica que esta ruta sea correcta
                            console.log("Imagen URL:", imageUrl);  // Agrega un log para verificar la URL
                            return `
                                <div class="menu-item">
                                    <img src="${imageUrl}" ">
                                    <h3>${item.name}</h3>
                                    <p>${item.description}</p>
                                    <span class="price">$${item.price}</span>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
                menuContainer.appendChild(section);
            }
        })
        .catch(error => {
            console.error("Error al cargar los datos del menú:", error);
            menuContainer.innerHTML = "<p>Error al cargar el menú. Intenta nuevamente más tarde.</p>";
        });

    // Formulario para agregar un producto
    document.getElementById("product-form")?.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Capturar datos del formulario
        const formData = new FormData(e.target);

        // Enviar datos a la API
        try {
            const response = await fetch("api.php", {
                method: "POST",
                body: formData,
            });

            if (!response.ok) {
                throw new Error("Error al agregar el producto.");
            }

            const result = await response.json();
            alert("Producto agregado exitosamente: " + result.message);

            // Opcional: Limpiar el formulario
            e.target.reset();
        } catch (error) {
            alert("Ocurrió un error: " + error.message);
        }
    });

});
