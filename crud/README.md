# CRUD Documentation Index

This directory contains comprehensive guides for implementing CRUD operations with ShadcnUI components in the Laravel modular architecture.

## 📚 Available Guides

### 1. [Form Validation Guide](./FORM_VALIDATION_SHADCN.md) 🆕
**Purpose**: Complete guide for form validation when migrating from Vuetify to ShadcnUI
**Contents**:
- Common issues and solutions
- Create/Edit/Delete form patterns
- Validation schema patterns
- Component binding patterns
- Troubleshooting checklist

**Use this when**: You're having form validation issues or starting a new CRUD module migration

---

### 2. [Creating Index Pages Guide](./creating-index-pages.md)
**Purpose**: Templates for creating Index.vue pages with data tables
**Contents**:
- Controller implementation patterns
- Vue Index page templates
- Route configuration
- Resource implementation
- Middleware setup

**Use this when**: You need to create a new index/listing page

---

### 3. [ShadcnUI CRUD Patterns](./SHADCN_CRUD_PATTERNS.md)
**Purpose**: Comprehensive patterns for CRUD operations using ShadcnUI
**Contents**:
- Complete CRUD templates
- Component patterns
- Best practices
- Common pitfalls to avoid

**Use this when**: You need reference patterns for any CRUD operation

---

### 4. [Batch Refactor Script](./BATCH_REFACTOR_SCRIPT.md)
**Purpose**: Automated scripts for batch refactoring Vuetify to ShadcnUI
**Contents**:
- Batch conversion scripts
- Pattern matching rules
- Automated replacement patterns

**Use this when**: You need to convert multiple files at once

---

### 5. [Restaurant Refactor Status](./RESTAURANT_REFACTOR_STATUS.md)
**Purpose**: Case study of Restaurant module refactoring
**Contents**:
- Refactoring progress tracking
- Lessons learned
- Common issues encountered

**Use this when**: You want to see a real-world refactoring example

---

## 🚀 Quick Start for New CRUD Module

1. **Start with Form Validation Guide** - Understand the validation patterns
2. **Use Creating Index Pages Guide** - Build your listing page
3. **Reference ShadcnUI CRUD Patterns** - Get component templates
4. **Check Form Validation Guide** - Debug any form issues

## 📝 Common Tasks

### Creating a New Module with CRUD

1. Read [Form Validation Guide](./FORM_VALIDATION_SHADCN.md) first
2. Use templates from [Creating Index Pages](./creating-index-pages.md)
3. Follow patterns in [ShadcnUI CRUD Patterns](./SHADCN_CRUD_PATTERNS.md)

### Migrating Existing Vuetify Module

1. Review [Form Validation Guide](./FORM_VALIDATION_SHADCN.md) for common issues
2. Use [Batch Refactor Script](./BATCH_REFACTOR_SCRIPT.md) for bulk changes
3. Check [Restaurant Refactor Status](./RESTAURANT_REFACTOR_STATUS.md) for examples

### Debugging Form Issues

1. Check [Form Validation Guide](./FORM_VALIDATION_SHADCN.md) troubleshooting section
2. Verify patterns match [ShadcnUI CRUD Patterns](./SHADCN_CRUD_PATTERNS.md)

## 🔍 Most Common Issues (Quick Reference)

| Issue | Solution | Guide |
|-------|----------|-------|
| Submit button not enabling | Check required fields & validation | [Form Validation Guide](./FORM_VALIDATION_SHADCN.md#issue-1-submit-button-not-working) |
| Switch shows wrong state | Use `:model-value` not `:checked` | [Form Validation Guide](./FORM_VALIDATION_SHADCN.md#issue-3-switchtoggle-components-not-working) |
| Double currency symbols | Check backend formatting | [Form Validation Guide](./FORM_VALIDATION_SHADCN.md#issue-4-double-currency-symbols) |
| Form values not updating | Use both `v-bind` and `v-model` | [Form Validation Guide](./FORM_VALIDATION_SHADCN.md#issue-2-form-values-not-updating) |

## 📋 Migration Checklist

- [ ] Read Form Validation Guide
- [ ] Set up validation schema (no `.nullable().required()`)
- [ ] Use `:initial-values="form"` not `form.data()`
- [ ] Add default values for required fields
- [ ] Use correct component props (model-value vs checked)
- [ ] Test all CRUD operations
- [ ] Check error handling and display
- [ ] Verify backend integration

## 💡 Tips

1. **Always test form submission** with browser console open
2. **Check validation rules** for contradictions
3. **Provide defaults** for required fields
4. **Follow existing patterns** in successfully migrated modules
5. **Use the troubleshooting checklist** when stuck

---

**Last Updated**: December 2024
**Maintained By**: Development Team