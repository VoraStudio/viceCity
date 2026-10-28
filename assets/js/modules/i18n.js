// Idiomes: diccionaris JSON a assets/i18n i textos marcats amb data-i18n / data-i18n-attr
export const SUPPORTED_LANGUAGES = ["ca", "es", "en"];
const DEFAULT_LANGUAGE = "ca";
const STORAGE_KEY = "vicity-lang";
const CHANGE_EVENT = "languagechange";
const AUTONYMS_PATH = "language.names";

const loadedDictionaries = {};
let currentLanguage = DEFAULT_LANGUAGE;
let latestRequest = 0;

export const isSupportedLanguage = (lang) => SUPPORTED_LANGUAGES.includes(lang);

// Un valor buit compta com a absent: les claus pendents de traduir es deixen a ""
export const lookupKey = (dictionary, key) => {
  let node = dictionary;

  for (const part of key.split(".")) {
    if (typeof node !== "object" || node === null || !Object.hasOwn(node, part)) return undefined;
    node = node[part];
  }

  return typeof node === "string" && node !== "" ? node : undefined;
};

const hasText = (node, path) => {
  if (typeof node === "string") return node !== "";
  if (typeof node !== "object" || node === null) return false;

  return Object.entries(node).some(([key, value]) => {
    const childPath = path ? `${path}.${key}` : key;
    return childPath !== AUTONYMS_PATH && hasText(value, childPath);
  });
};

// Els autònims són a tots els diccionaris: només compta el text que s'ha traduït de debò
export const hasTranslations = (dictionary) => typeof dictionary === "object" && hasText(dictionary, "");

export const findTranslation = (key, language, dictionaries) =>
  lookupKey(dictionaries[language], key) ?? lookupKey(dictionaries[DEFAULT_LANGUAGE], key);

export const resolveTranslation = (key, language, dictionaries) => findTranslation(key, language, dictionaries) ?? key;

export const parseAttributeSpec = (spec) =>
  (spec ?? "").split(";").flatMap((entry) => {
    const separator = entry.indexOf(":");
    if (separator === -1) return [];

    const attribute = entry.slice(0, separator).trim();
    const key = entry.slice(separator + 1).trim();
    return attribute && key ? [{ attribute, key }] : [];
  });

const selectWithin = (root, selector) => {
  const self = root.matches?.(selector) ? [root] : [];
  return [...self, ...root.querySelectorAll(selector)];
};

const applyText = (element) => {
  const text = findTranslation(element.dataset.i18n, currentLanguage, loadedDictionaries);
  if (text === undefined || element.textContent === text) return;

  element.textContent = text;
};

const applyAttributes = (element) => {
  parseAttributeSpec(element.dataset.i18nAttr).forEach(({ attribute, key }) => {
    const value = findTranslation(key, currentLanguage, loadedDictionaries);
    if (value !== undefined) element.setAttribute(attribute, value);
  });
};

export const applyTranslations = (root = document) => {
  selectWithin(root, "[data-i18n]").forEach(applyText);
  selectWithin(root, "[data-i18n-attr]").forEach(applyAttributes);
};

const loadDictionary = async (lang) => {
  if (loadedDictionaries[lang]) return;

  try {
    const response = await fetch(new URL(`../../i18n/${lang}.json`, import.meta.url));
    if (!response.ok) return;
    loadedDictionaries[lang] = await response.json();
  } catch {
    // Sense diccionari es manté el text català que ja té el HTML
  }
};

const readSavedLanguage = () => {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    return isSupportedLanguage(saved) ? saved : null;
  } catch {
    return null;
  }
};

const saveLanguage = (lang) => {
  try {
    localStorage.setItem(STORAGE_KEY, lang);
  } catch {
    // Emmagatzematge bloquejat: l'elecció només dura fins que es recarregui la pàgina
  }
};

// Només l'última petició s'aplica: una càrrega lenta no pot trepitjar una elecció posterior
const activateLanguage = async (lang) => {
  const request = ++latestRequest;
  await Promise.all([...new Set([DEFAULT_LANGUAGE, lang])].map(loadDictionary));
  if (request !== latestRequest || !loadedDictionaries[lang]) return false;

  currentLanguage = lang;
  // <html lang> només canvia si hi ha traduccions: un esquelet buit continua mostrant text català
  document.documentElement.lang = hasTranslations(loadedDictionaries[lang]) ? lang : DEFAULT_LANGUAGE;
  applyTranslations(document);
  return true;
};

// Només a document i sense bubbling: no es barreja amb el languagechange natiu de window
const announceLanguage = () => {
  document.dispatchEvent(new CustomEvent(CHANGE_EVENT, { detail: { lang: currentLanguage } }));
};

export const getLanguage = () => currentLanguage;

export const t = (key) => resolveTranslation(key, currentLanguage, loadedDictionaries);

export const initI18n = async () => {
  const activated = await activateLanguage(readSavedLanguage() ?? DEFAULT_LANGUAGE);
  if (activated) announceLanguage();
};

export const setLanguage = async (lang) => {
  if (!isSupportedLanguage(lang)) return;

  const activated = await activateLanguage(lang);
  if (!activated) return;

  saveLanguage(lang);
  announceLanguage();
};
