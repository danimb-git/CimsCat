/**
 * GESTIÓ DE LIKES I COMENTARIS
 * Versió amb controllers dins de public/controllers/
 */

// ======================
// GESTIÓ DE LIKES
// ======================

function toggleLike(idExcursio, boton) {
    const formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('id_excursio', idExcursio);

    // PATH: controllers/ (dins de public/)
    fetch('controllers/LikeController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.liked) {
                boton.classList.add('activo');
                boton.setAttribute('aria-label', 'Eliminar dels preferits');
            } else {
                boton.classList.remove('activo');
                boton.setAttribute('aria-label', 'Afegir als preferits');
            }

            const comptador = boton.querySelector('.like-count');
            if (comptador) {
                comptador.textContent = data.count;
            }

            mostrarNotificacio(
                data.action === 'added' ? 'Afegit als preferits' : 'Eliminat dels preferits',
                'success'
            );
        } else {
            mostrarNotificacio(data.error || 'Error al processar el like', 'error');
            
            if (data.error && data.error.includes('iniciar sessió')) {
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            }
        }
    })
    .catch(error => {
        console.error('❌ Error en toggleLike:', error);
        mostrarNotificacio('Error de connexió amb el servidor', 'error');
    });
}

function carregarEstatsLikes() {
    const botonsLike = document.querySelectorAll('[data-excursio-id]');
    
    botonsLike.forEach(boton => {
        const idExcursio = boton.getAttribute('data-excursio-id');
        
        fetch(`controllers/LikeController.php?action=check&id_excursio=${idExcursio}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.liked) {
                    boton.classList.add('activo');
                }
            })
            .catch(error => console.error('Error carregant estat like:', error));

        fetch(`controllers/LikeController.php?action=count&id_excursio=${idExcursio}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const comptador = boton.querySelector('.like-count');
                    if (comptador) {
                        comptador.textContent = data.count;
                    }
                }
            })
            .catch(error => console.error('Error carregant comptador:', error));
    });
}

// ======================
// GESTIÓ DE COMENTARIS
// ======================

function carregarComentaris(idExcursio, contenidor) {
    fetch(`controllers/ComentariController.php?action=list&id_excursio=${idExcursio}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarComentaris(data.comentaris, contenidor);
                
                const comptador = document.querySelector(`[data-count-excursio="${idExcursio}"]`);
                if (comptador) {
                    comptador.textContent = `${data.total} comentari${data.total !== 1 ? 's' : ''}`;
                }
            } else {
                contenidor.innerHTML = '<p class="error">Error al carregar els comentaris</p>';
            }
        })
        .catch(error => {
            console.error('❌ Error carregant comentaris:', error);
            contenidor.innerHTML = '<p class="error">Error de connexió. Comprova que els controllers estan instal·lats.</p>';
        });
}

function mostrarComentaris(comentaris, contenidor) {
    if (comentaris.length === 0) {
        contenidor.innerHTML = '<p class="sin-comentarios">Encara no hi ha comentaris. Sigues el primer!</p>';
        return;
    }

    const html = comentaris.map(comentari => `
        <article class="comentari" data-comentari-id="${comentari.id}">
            <div class="comentari__header">
                <strong class="comentari__autor">${escapeHtml(comentari.nom_usuari)}</strong>
                <time class="comentari__data">${formatarData(comentari.data)}</time>
            </div>
            <p class="comentari__contingut">${escapeHtml(comentari.contingut)}</p>
            <div class="comentari__accions">
                ${crearBotonsDAccio(comentari)}
            </div>
        </article>
    `).join('');

    contenidor.innerHTML = html;
}

function crearBotonsDAccio(comentari) {
    const usuariActual = window.USER_ID || null;
    const rolActual = window.USER_ROL || null;

    if (!usuariActual) {
        return '';
    }

    let botons = '';

    if (comentari.id_usuari == usuariActual) {
        botons += `
            <button class="boton-petit boton-perill" onclick="eliminarComentari(${comentari.id}, ${comentari.id_excursio})">
                Eliminar
            </button>
        `;
    } else if (rolActual === 'administrador') {
        botons += `
            <button class="boton-petit boton-perill" onclick="eliminarComentari(${comentari.id}, ${comentari.id_excursio})">
                Eliminar
            </button>
        `;
    }

    return botons;
}

function crearComentari(idExcursio, contingut, callback) {
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('id_excursio', idExcursio);
    formData.append('contingut', contingut);

    fetch('controllers/ComentariController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacio('Comentari afegit correctament', 'success');
            if (callback) callback(data);
        } else {
            mostrarNotificacio(data.error || 'Error al crear el comentari', 'error');
            
            if (data.error && data.error.includes('iniciar sessió')) {
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            }
        }
    })
    .catch(error => {
        console.error('❌ Error creant comentari:', error);
        mostrarNotificacio('Error de connexió amb el servidor', 'error');
    });
}

function eliminarComentari(idComentari, idExcursio) {
    if (!confirm('Estàs segur que vols eliminar aquest comentari?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', idComentari);

    fetch('controllers/ComentariController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacio('Comentari eliminat correctament', 'success');
            
            const contenidor = document.getElementById('comentaris-container');
            if (contenidor) {
                carregarComentaris(idExcursio, contenidor);
            }
        } else {
            mostrarNotificacio(data.error || 'Error al eliminar el comentari', 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error eliminant comentari:', error);
        mostrarNotificacio('Error de connexió amb el servidor', 'error');
    });
}

// ======================
// FUNCIONS AUXILIARS
// ======================

function mostrarNotificacio(missatge, tipus = 'info') {
    const notificacio = document.createElement('div');
    notificacio.className = `notificacio notificacio--${tipus}`;
    notificacio.textContent = missatge;
    
    document.body.appendChild(notificacio);
    
    setTimeout(() => {
        notificacio.classList.add('mostrar');
    }, 10);
    
    setTimeout(() => {
        notificacio.classList.remove('mostrar');
        setTimeout(() => {
            notificacio.remove();
        }, 300);
    }, 3000);
}

function formatarData(data) {
    const date = new Date(data);
    const ara = new Date();
    const diferencia = ara - date;
    const dies = Math.floor(diferencia / (1000 * 60 * 60 * 24));
    
    if (dies === 0) return 'Avui';
    if (dies === 1) return 'Ahir';
    if (dies < 7) return `Fa ${dies} dies`;
    
    return date.toLocaleDateString('ca-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ======================
// INICIALITZACIÓ
// ======================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicialitzant sistema de likes i comentaris...');
    
    carregarEstatsLikes();

    document.querySelectorAll('.boton-corazon').forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            const idExcursio = this.getAttribute('data-excursio-id');
            if (idExcursio) {
                toggleLike(idExcursio, this);
            } else {
                console.error('❌ Botó sense data-excursio-id');
            }
        });
    });

    const comentarisContainer = document.getElementById('comentaris-container');
    if (comentarisContainer) {
        const idExcursio = comentarisContainer.getAttribute('data-excursio-id');
        if (idExcursio) {
            console.log('📝 Carregant comentaris per excursió:', idExcursio);
            carregarComentaris(idExcursio, comentarisContainer);
        } else {
            console.error('❌ Contenidor de comentaris sense data-excursio-id');
        }
    }

    const formComentari = document.getElementById('form-comentari');
    if (formComentari) {
        formComentari.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const idExcursio = this.getAttribute('data-excursio-id');
            const textarea = this.querySelector('textarea[name="contingut"]');
            const contingut = textarea.value.trim();
            
            if (contingut.length < 3) {
                mostrarNotificacio('El comentari ha de tenir almenys 3 caràcters', 'error');
                return;
            }
            
            if (contingut.length > 200) {
                mostrarNotificacio('El comentari no pot superar els 200 caràcters', 'error');
                return;
            }
            
            console.log('✍️ Creant comentari:', {idExcursio, contingut});
            
            crearComentari(idExcursio, contingut, () => {
                textarea.value = '';
                
                const counter = document.getElementById('char-count');
                if (counter) {
                    counter.textContent = '0';
                }
                
                const contenidor = document.getElementById('comentaris-container');
                if (contenidor) {
                    carregarComentaris(idExcursio, contenidor);
                }
            });
        });
    }
    
    console.log('✅ Sistema inicialitzat correctament');
});