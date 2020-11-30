#!/usr/bin/env bash

set -eo pipefail

[[ -n $DEBUG ]] && set -x

shopt -s extglob

FILENAME_SUFFIX='enc'
FILE_EXT='yaml'

usage(){
    echo "Usage:"
    echo ""
    echo "  [env] sops <command>"
    echo ""
    echo "The commands are:"
    echo ""
    echo "  encrypt       (e)   Encrypt all secrets that are stored in the .secrets/sops dir"
    echo "  decrypt       (d)   Decrypt all secrets that are stored in the .secrets/sops dir"
    echo ""
    echo "Environment variables:"
    echo ""
    echo "  \$DEBUG       Set logging to verbose. (optional)"
}

encrypt() {
    # TODO: support encrypting a secret with an explicit path in the repo
    for FILE in .secrets/sops/*."${FILE_EXT}"; do
        # strip file suffix and extension
        FILENAME=${FILE%.$FILE_EXT}
        [[ ${FILE} =~ ${FILENAME_SUFFIX} ]] && echo "Skipping already encrypted file: ${FILE}" && continue
        sops --encrypt "${FILE}" > "${FILENAME}.${FILENAME_SUFFIX}.${FILE_EXT}"
        # remove unencrypted value
        rm -f "${FILE}"
    done
}

decrypt() {
    mkdir -p .secrets/sops/unencrypted
    for FILE in .secrets/sops/*."${FILENAME_SUFFIX}"."${FILE_EXT}"; do
        # strip file suffix and extension
        FILENAME=${FILE%.$FILENAME_SUFFIX.$FILE_EXT}
        # strip parent dir paths
        FILENAME=${FILENAME##*/}
        sops --decrypt "${FILE}" > .secrets/sops/unencrypted/"${FILENAME}.${FILE_EXT}"
    done
}

case "${1:-}" in
    encrypt|e)
        encrypt
        ;;
    decrypt|d)
        decrypt
        ;;
    *)
        usage >&2
        exit 1
        ;;
esac
