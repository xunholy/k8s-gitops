package lib.test

empty(value) {
  count(value) == 0
}

no_violations(violation) {
  empty(violation)
}

violations(violation) {
  not empty(violation)
}
