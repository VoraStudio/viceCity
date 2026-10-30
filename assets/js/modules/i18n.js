// Idiomes: diccionaris JSON a assets/i18n. El català és la base i ja és al HTML.
// Canviar d'idioma desa l'elecció i recarrega la pàgina: els textos s'apliquen abans de les animacions.
export const LANGUAGES = { ca: "Català", es: "Castellano", en: "English", fr: "Français" };
// BASE_LANGUAGE és el que hi ha escrit al HTML i el respatller de les claus buides; DEFAULT_LANGUAGE, l'idioma de qui no n'ha triat cap
const BASE_LANGUAGE = "ca";
const DEFAULT_LANGUAGE = "ca";
const STORAGE_KEY = "vicity-lang";
const DATE_FORMAT = { day: "numeric", month: "short", year: "numeric", timeZone: "UTC" };

const dictionaries = {};

// Un valor buit compta com a absent: les claus pendents de traduir es deixen a ""
const lookup = (dictionary, key) => {
  let node = dictionary;

  for (const part of key.split(".")) {
    if (typeof node !== "object" || node === null || !Object.hasOwn(node, part)) return undefined;
    node = node[part];
  }

  return typeof node === "string" && node !== "" ? node : undefined;
};

export const findTranslation = (key, language, sources) =>
  lookup(sources[language], key) ?? lookup(sources[BASE_LANGUAGE], key);

export const fillPlaceholders = (text, params) =>
  text.replace(/\{(\w+)\}/g, (placeholder, name) => (Object.hasOwn(params, name) ? String(params[name]) : placeholder));

export const parseAttributeSpec = (spec = "") =>
  spec.split(";").flatMap((entry) => {
    const [attribute = "", key = ""] = entry.split(":").map((part) => part.trim());
    return attribute && key ? [{ attribute, key }] : [];
  });

export const getLanguage = () => {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    return Object.hasOwn(LANGUAGES, saved) ? saved : DEFAULT_LANGUAGE;
  } catch {
    return DEFAULT_LANGUAGE;
  }
};

export const switchLanguage = (language) => {
  if (!Object.hasOwn(LANGUAGES, language)) return;

  try {
    localStorage.setItem(STORAGE_KEY, language);
  } catch {
    // Emmagatzematge bloquejat: en recarregar es tornarà a l'idioma per defecte
  }
  location.reload();
};

// Text per a JS: idioma actiu → català → la clau mateixa
export const t = (key, params = {}) => fillPlaceholders(findTranslation(key, getLanguage(), dictionaries) ?? key, params);

const loadDictionary = async (language) => {
  try {
    // no-cache: el navegador revalida el diccionari i els canvis del client es veuen de seguida
    const response = await fetch(new URL(`../../i18n/${language}.json`, import.meta.url), { cache: "no-cache" });
    if (response.ok) dictionaries[language] = await response.json();
  } catch {
    // Sense diccionari es manté el text català que ja té el HTML
  }
};

const translateAttributes = (element, language) => {
  parseAttributeSpec(element.dataset.i18nAttr).forEach(({ attribute, key }) => {
    const value = findTranslation(key, language, dictionaries);
    if (value === undefined || element.getAttribute(attribute) === value) return;

    element.setAttribute(attribute, value);
    // Un <source> nou no es carrega sol: el vídeo ha de tornar a triar la font
    if (element.localName === "source") element.parentElement.load();
  });
};

// data-i18n-html només per als textos amb marcatge en línia: els diccionaris són del repositori, no de l'usuari
const applyTranslations = (language) => {
  const translate = (key) => findTranslation(key, language, dictionaries);

  document.querySelectorAll("[data-i18n]").forEach((element) => {
    const text = translate(element.dataset.i18n);
    if (text !== undefined) element.textContent = text;
  });
  document.querySelectorAll("[data-i18n-html]").forEach((element) => {
    const html = translate(element.dataset.i18nHtml);
    if (html !== undefined) element.innerHTML = html;
  });
  document.querySelectorAll("[data-i18n-attr]").forEach((element) => translateAttributes(element, language));

  const dateFormat = new Intl.DateTimeFormat(language, DATE_FORMAT);
  document.querySelectorAll("time[datetime]").forEach((time) => {
    time.textContent = dateFormat.format(new Date(time.dateTime));
  });
};

export const initI18n = async () => {
  const language = getLanguage();
  await Promise.all([...new Set([BASE_LANGUAGE, language])].map(loadDictionary));
  if (language === BASE_LANGUAGE || !dictionaries[language]) return;

  document.documentElement.lang = language;
  applyTranslations(language);
};
