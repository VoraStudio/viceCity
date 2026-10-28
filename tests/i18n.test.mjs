import assert from "node:assert/strict";
import { describe, it } from "node:test";
import {
  SUPPORTED_LANGUAGES,
  findTranslation,
  hasTranslations,
  isSupportedLanguage,
  lookupKey,
  parseAttributeSpec,
  resolveTranslation,
} from "../assets/js/modules/i18n.js";

describe("SUPPORTED_LANGUAGES", () => {
  it("lists the languages in display order with Catalan first", () => {
    assert.deepEqual(SUPPORTED_LANGUAGES, ["ca", "es", "en"]);
  });
});

describe("hasTranslations", () => {
  const autonyms = { ca: "Català", es: "Castellano", en: "English" };

  it("is false for a skeleton that only has autonyms and empty values", () => {
    assert.equal(hasTranslations({ language: { label: "", names: autonyms } }), false);
  });

  it("is true as soon as one value outside language.names has text", () => {
    assert.equal(hasTranslations({ language: { label: "Idioma", names: autonyms } }), true);
    assert.equal(hasTranslations({ language: { label: "", names: autonyms }, hero: { title: "Hola" } }), true);
  });

  it("finds text at any depth", () => {
    assert.equal(hasTranslations({ a: { b: { c: { d: "deep" } } } }), true);
    assert.equal(hasTranslations({ a: { b: { c: { d: "" } } } }), false);
  });

  it("only excludes the exact language.names path from the root", () => {
    assert.equal(hasTranslations({ language: { namesExtra: "x" } }), true);
    assert.equal(hasTranslations({ other: { language: { names: { ca: "x" } } } }), true);
    assert.equal(hasTranslations({ language: { names: { ca: "x" } } }), false);
  });

  it("ignores non-string leaves", () => {
    assert.equal(hasTranslations({ count: 3, flag: true, nothing: null }), false);
  });

  it("is false for empty or missing dictionaries", () => {
    for (const value of [{}, undefined, null, "", "text-at-root"]) {
      assert.equal(hasTranslations(value), false, String(value));
    }
  });
});

describe("isSupportedLanguage", () => {
  it("accepts the three supported languages", () => {
    assert.equal(isSupportedLanguage("ca"), true);
    assert.equal(isSupportedLanguage("es"), true);
    assert.equal(isSupportedLanguage("en"), true);
  });

  it("rejects anything else, including near misses and non-strings", () => {
    for (const value of ["fr", "", "CA", "ca ", "ca-ES", null, undefined, 42]) {
      assert.equal(isSupportedLanguage(value), false, String(value));
    }
  });
});

describe("lookupKey", () => {
  const dictionary = {
    title: "Títol",
    language: { label: "Idioma", names: { ca: "Català" } },
    empty: "",
    count: 3,
  };

  it("reads top-level and nested keys by dotted path", () => {
    assert.equal(lookupKey(dictionary, "title"), "Títol");
    assert.equal(lookupKey(dictionary, "language.label"), "Idioma");
    assert.equal(lookupKey(dictionary, "language.names.ca"), "Català");
  });

  it("returns undefined for unknown keys and unknown intermediate nodes", () => {
    assert.equal(lookupKey(dictionary, "nope"), undefined);
    assert.equal(lookupKey(dictionary, "language.nope"), undefined);
    assert.equal(lookupKey(dictionary, "nope.deeper.still"), undefined);
    assert.equal(lookupKey(dictionary, "title.deeper"), undefined);
  });

  it("treats an empty string as missing", () => {
    assert.equal(lookupKey(dictionary, "empty"), undefined);
  });

  it("returns undefined when the path points to a namespace or a non-string", () => {
    assert.equal(lookupKey(dictionary, "language"), undefined);
    assert.equal(lookupKey(dictionary, "count"), undefined);
  });

  it("returns undefined when the dictionary is not loaded", () => {
    assert.equal(lookupKey(undefined, "title"), undefined);
    assert.equal(lookupKey(null, "title"), undefined);
  });

  it("never walks the prototype chain", () => {
    assert.equal(lookupKey({}, "constructor"), undefined);
    assert.equal(lookupKey({}, "toString.name"), undefined);
    assert.equal(lookupKey({}, "__proto__"), undefined);
  });
});

describe("findTranslation", () => {
  const dictionaries = {
    ca: { greeting: "Hola", only: { catalan: "Només català" }, blank: "" },
    es: { greeting: "Buenas", only: {}, blank: "" },
    en: { greeting: "" },
  };

  it("uses the active language when it has the key", () => {
    assert.equal(findTranslation("greeting", "es", dictionaries), "Buenas");
  });

  it("falls back to Catalan when the active language leaves the value empty", () => {
    assert.equal(findTranslation("greeting", "en", dictionaries), "Hola");
  });

  it("falls back to Catalan when the active language lacks the key", () => {
    assert.equal(findTranslation("only.catalan", "es", dictionaries), "Només català");
  });

  it("falls back to Catalan when the active dictionary is not loaded", () => {
    assert.equal(findTranslation("greeting", "en", { ca: dictionaries.ca }), "Hola");
  });

  it("returns undefined when no dictionary has a usable value", () => {
    assert.equal(findTranslation("blank", "es", dictionaries), undefined);
    assert.equal(findTranslation("unknown.key", "es", dictionaries), undefined);
    assert.equal(findTranslation("greeting", "es", {}), undefined);
  });

  it("does not loop when the active language is Catalan", () => {
    assert.equal(findTranslation("greeting", "ca", dictionaries), "Hola");
    assert.equal(findTranslation("blank", "ca", dictionaries), undefined);
  });
});

describe("resolveTranslation", () => {
  const dictionaries = { ca: { greeting: "Hola" }, es: { greeting: "Buenas" } };

  it("returns the translation of the active language", () => {
    assert.equal(resolveTranslation("greeting", "es", dictionaries), "Buenas");
  });

  it("falls back to Catalan", () => {
    assert.equal(resolveTranslation("greeting", "en", dictionaries), "Hola");
  });

  it("returns the key itself as the last resort", () => {
    assert.equal(resolveTranslation("unknown.key", "es", dictionaries), "unknown.key");
    assert.equal(resolveTranslation("greeting", "es", {}), "greeting");
  });
});

describe("parseAttributeSpec", () => {
  it("parses attribute:key pairs separated by semicolons", () => {
    assert.deepEqual(parseAttributeSpec("alt:image.alt;aria-label:menu.open"), [
      { attribute: "alt", key: "image.alt" },
      { attribute: "aria-label", key: "menu.open" },
    ]);
  });

  it("parses a single pair", () => {
    assert.deepEqual(parseAttributeSpec("title:nav.title"), [{ attribute: "title", key: "nav.title" }]);
  });

  it("trims whitespace around attributes and keys", () => {
    assert.deepEqual(parseAttributeSpec(" alt : a.b ; title : c "), [
      { attribute: "alt", key: "a.b" },
      { attribute: "title", key: "c" },
    ]);
  });

  it("skips empty and malformed entries", () => {
    assert.deepEqual(parseAttributeSpec("alt:a.b;;broken;:no.attribute;no-key:; ;title:c;"), [
      { attribute: "alt", key: "a.b" },
      { attribute: "title", key: "c" },
    ]);
  });

  it("returns an empty list for empty or missing specs", () => {
    assert.deepEqual(parseAttributeSpec(""), []);
    assert.deepEqual(parseAttributeSpec(null), []);
    assert.deepEqual(parseAttributeSpec(undefined), []);
  });
});
