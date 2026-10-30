import assert from "node:assert/strict";
import { describe, it } from "node:test";
import { LANGUAGES, fillPlaceholders, findTranslation, parseAttributeSpec } from "../assets/js/modules/i18n.js";

describe("LANGUAGES", () => {
  it("lists the languages with their autonyms, Catalan first", () => {
    assert.deepEqual(LANGUAGES, { ca: "Català", es: "Castellano", en: "English", fr: "Français" });
    assert.deepEqual(Object.keys(LANGUAGES), ["ca", "es", "en", "fr"]);
  });
});

describe("findTranslation: lookup by dotted key", () => {
  const dictionaries = {
    ca: {
      title: "Títol",
      language: { label: "Idioma", nested: { deep: "Profund" } },
      empty: "",
      count: 3,
    },
  };
  const find = (key) => findTranslation(key, "ca", dictionaries);

  it("reads top-level and nested keys", () => {
    assert.equal(find("title"), "Títol");
    assert.equal(find("language.label"), "Idioma");
    assert.equal(find("language.nested.deep"), "Profund");
  });

  it("returns undefined for unknown keys and unknown intermediate nodes", () => {
    assert.equal(find("nope"), undefined);
    assert.equal(find("language.nope"), undefined);
    assert.equal(find("nope.deeper.still"), undefined);
    assert.equal(find("title.deeper"), undefined);
  });

  it("treats an empty string as missing", () => {
    assert.equal(find("empty"), undefined);
  });

  it("returns undefined when the key points to a namespace or a non-string", () => {
    assert.equal(find("language"), undefined);
    assert.equal(find("count"), undefined);
  });

  it("never walks the prototype chain", () => {
    assert.equal(find("constructor"), undefined);
    assert.equal(find("toString.name"), undefined);
    assert.equal(find("__proto__"), undefined);
    assert.equal(find("title.length"), undefined);
  });
});

describe("findTranslation: fallback to Catalan", () => {
  const dictionaries = {
    ca: { greeting: "Hola", only: { catalan: "Només català" }, blank: "" },
    es: { greeting: "Buenas", only: {}, blank: "" },
    en: { greeting: "" },
  };

  it("uses the active language when it has the value", () => {
    assert.equal(findTranslation("greeting", "es", dictionaries), "Buenas");
  });

  it("falls back to Catalan when the active value is empty", () => {
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

  it("works when the active language is Catalan itself", () => {
    assert.equal(findTranslation("greeting", "ca", dictionaries), "Hola");
    assert.equal(findTranslation("blank", "ca", dictionaries), undefined);
  });
});

describe("fillPlaceholders", () => {
  it("replaces each {name} with its value", () => {
    assert.equal(fillPlaceholders("Targeta {current} de {total}", { current: 2, total: 4 }), "Targeta 2 de 4");
  });

  it("replaces a placeholder every time it appears", () => {
    assert.equal(fillPlaceholders("{a} i {a}", { a: "x" }), "x i x");
  });

  it("leaves unknown placeholders untouched", () => {
    assert.equal(fillPlaceholders("Hola {name}", {}), "Hola {name}");
  });

  it("does not read inherited properties", () => {
    assert.equal(fillPlaceholders("{constructor}", {}), "{constructor}");
  });

  it("returns text without placeholders as is", () => {
    assert.equal(fillPlaceholders("Sense res", { a: 1 }), "Sense res");
  });
});

describe("parseAttributeSpec", () => {
  it("parses attribute:key pairs separated by semicolons", () => {
    assert.deepEqual(parseAttributeSpec("alt:image.alt;aria-label:menu.open"), [
      { attribute: "alt", key: "image.alt" },
      { attribute: "aria-label", key: "menu.open" },
    ]);
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
    assert.deepEqual(parseAttributeSpec(undefined), []);
  });
});
