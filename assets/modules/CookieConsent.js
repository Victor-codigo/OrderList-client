import * as CookieConsent from 'App/Dependencies/vanilla-cookieconsent/dist/cookieconsent.esm'

export function load() {
    CookieConsent.run({
        guiOptions: {
            consentModal: {
                layout: "box",
                position: "bottom left",
                equalWeightButtons: true,
                flipButtons: false
            },
            preferencesModal: {
                layout: "box",
                position: "right",
                equalWeightButtons: true,
                flipButtons: false
            }
        },
        categories: {
            necessary: {
                readOnly: true
            }
        },
        language: {
            default: "en",
            translations: {
                en: {
                    consentModal: {
                        title: "Consentimiento de cookies",
                        description: "We are using esencial cookies to improve and customize your visit, and to analyze the preformace of the website.\n<br><br>\nYou can accept all cookies clicking on button \"Acept all\" or manage preferences.",
                        closeIconLabel: "",
                        acceptAllBtn: "Accept all",
                        acceptNecessaryBtn: "Reject all",
                        showPreferencesBtn: "Manage preferences",
                        footer: "<a href=\"/es/legal/privacy\">Privacy Policy</a>\n<a href=\"/en/legal/notice\">Terms and conditions</a>"
                    },
                    preferencesModal: {
                        title: "Consent Preferences Center",
                        closeIconLabel: "Close modal",
                        acceptAllBtn: "Accept all",
                        acceptNecessaryBtn: "Reject all",
                        savePreferencesBtn: "Save preferences",
                        serviceCounterLabel: "Service|Services",
                        sections: [
                            {
                                title: "Cookie Usage",
                                description: "We use cookies to customize the content and analyze the traffic"
                            },
                            {
                                title: "Strictly Necessary Cookies <span class=\"pm__badge\">Always Enabled</span>",
                                description: "Necessary cookies make the website usable, allowing basic functionality like navigation and the access to restricted areas. The Website cannot be functional without these cookies.",
                                linkedCategory: "necessary"
                            },
                            {
                                title: "More information",
                                description: "For more information about cookies policy, please go to <a class=\"cc__link\" href=\"/en/legal/privacy\">Privacy</a>."
                            }
                        ]
                    }
                },
                es: {
                    consentModal: {
                        title: "Consentimiento de cookies",
                        description: "\nUtilizamos cookies esenciales para mejorar y personalizar su visita, y para analizar el rendimiento de nuestro sitio web. \n<br><br>\nPuede aceptar todas las cookies haciendo clic en el botón \"Aceptar\" o gestionar la configuración de sus cookies.\n",
                        closeIconLabel: "",
                        acceptAllBtn: "Aceptar todo",
                        acceptNecessaryBtn: "Rechazar todo",
                        showPreferencesBtn: "Gestionar preferencias",
                        footer: "<a href=\"/es/legal/privacy\">Política de privacidad</a>\n<a href=\"/en/legal/notice\">Términos y condiciones</a>"
                    },
                    preferencesModal: {
                        title: "Preferencias de Consentimiento",
                        closeIconLabel: "Cerrar modal",
                        acceptAllBtn: "Aceptar todo",
                        acceptNecessaryBtn: "Rechazar todo",
                        savePreferencesBtn: "Guardar preferencias",
                        serviceCounterLabel: "Servicios",
                        sections: [
                            {
                                title: "Uso de Cookies",
                                description: "Utilizamos cookies para personalizar el contenido y analizar nuestro tráfico."
                            },
                            {
                                title: "Cookies Estrictamente Necesarias <span class=\"pm__badge\">Siempre Habilitado</span>",
                                description: "Las cookies necesarias ayudan a que un sitio web sea utilizable al permitir funciones básicas como la navegación por la página y el acceso a áreas seguras del sitio web. El sitio web no puede funcionar correctamente sin estas cookies.",
                                linkedCategory: "necessary"
                            },
                            {
                                title: "Más información",
                                description: "Para más información relacionada con la política de cookies, por favor dirígete a <a class=\"cc__link\" href=\"/en/legal/privacy\">Privacidad</a>."
                            }
                        ]
                    }
                }
            },
            autoDetect: "document"
        },
        disablePageInteraction: false
    });
}