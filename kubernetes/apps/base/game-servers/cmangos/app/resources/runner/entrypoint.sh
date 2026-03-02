#!/usr/bin/env bash
#

set -e

# Utils:
#
function echoerr()
{
    echo ${@} >&2
}

function success()
{
    echo -e "\e[32m${1}\e[0m"
}
function info()
{
    echo -e "\e[36m${1}\e[0m"
}
function warning()
{
    if [[ "${2}" == "--underline" ]]
    then
        echo -e "\e[4;33m${1}\e[0m"
    else
        echo -e "\e[33m${1}\e[0m"
    fi
}
function error()
{
    if [[ "${2}" == "--underline" ]]
    then
        echo -e "\e[4;31m${1}\e[0m"
    else
        echo -e "\e[31m${1}\e[0m"
    fi
}

function replace_conf()
{
    local SEARCH_FOR="${1}"
    local REPLACE_WITH="${2}"
    local FILENAME="${3}"

    sed -i "/^${SEARCH_FOR}/c\\${SEARCH_FOR} = ${REPLACE_WITH}" "${FILENAME}"
}
function merge_confs()
{
    local FILENAME="${1}"
    local CONFIG_FILE="${2}"

    while IFS='' read -r LINE || [[ -n "${LINE}" ]]
    do
        PROPERTY="$(echo "${LINE}" | cut -d '#' -f 1 | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"

        if [[ -n "${PROPERTY}" ]]
        then
            local SEARCH_FOR="$(echo "${PROPERTY}" | cut -d '=' -f 1 | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"
            local REPLACE_WITH="$(echo "${PROPERTY}" | cut -d '=' -f 2- | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"

            replace_conf "${SEARCH_FOR}" "${REPLACE_WITH}" "${FILENAME}"
        fi

    done < "${CONFIG_FILE}"
}

# Sub-functions:
#
function compose_generic_conf_file()
{
    local FILENAME="${1}"

    if [[ ! -f "/opt/mangos/conf/${FILENAME}" ]]
    then
        return
    fi

    cd "${MANGOS_DIR}/etc"

    if [[ ! -f "${FILENAME}.dist" ]]
    then
        echoerr ""
        echoerr -e " $(error "ERROR!" --underline)"
        echoerr -e "  $(error "└") The file \"$(info "${FILENAME}.dist")\" you're trying to"
        echoerr -e "     compose the configuration from doesn't exist."

        exit 2
    fi

    cp "${FILENAME}.dist" "${FILENAME}"

    merge_confs "${FILENAME}" "/opt/mangos/conf/${FILENAME}"
}

function compose_mangosd_conf_file()
{
    local MANGOS_DBCONN="${MANGOS_DBHOST};${MANGOS_DBPORT};${MANGOS_DBUSER};${MANGOS_DBPASS}"

    cd "${MANGOS_DIR}/etc"
    cp mangosd.conf.dist mangosd.conf

    replace_conf "LoginDatabaseInfo" "\"${MANGOS_DBCONN};${MANGOS_REALMD_DBNAME}\"" mangosd.conf
    replace_conf "WorldDatabaseInfo" "\"${MANGOS_DBCONN};${MANGOS_WORLD_DBNAME}\"" mangosd.conf
    replace_conf "CharacterDatabaseInfo" "\"${MANGOS_DBCONN};${MANGOS_CHARACTERS_DBNAME}\"" mangosd.conf
    replace_conf "LogsDatabaseInfo" "\"${MANGOS_DBCONN};${MANGOS_LOGS_DBNAME}\"" mangosd.conf

    if [[ -f "/opt/mangos/conf/mangosd.conf" ]]
    then
        merge_confs mangosd.conf "/opt/mangos/conf/mangosd.conf"
    fi
}
function compose_realmd_conf_file()
{
    local MANGOS_DBCONN="${MANGOS_DBHOST};${MANGOS_DBPORT};${MANGOS_DBUSER};${MANGOS_DBPASS}"

    cd "${MANGOS_DIR}/etc"
    cp realmd.conf.dist realmd.conf

    replace_conf "LoginDatabaseInfo" "\"${MANGOS_DBCONN};${MANGOS_REALMD_DBNAME}\"" realmd.conf

    if [[ -f "/opt/mangos/conf/realmd.conf" ]]
    then
        merge_confs realmd.conf "/opt/mangos/conf/realmd.conf"
    fi
}

function set_timezone()
{
    ln -snf "/usr/share/zoneinfo/${TZ}" /etc/localtime
    echo "${TZ}" > /etc/timezone

    dpkg-reconfigure --frontend noninteractive tzdata &> /dev/null
}

function wait_for_database()
{
    wait-for-it -h "${MANGOS_DBHOST}" -p "${MANGOS_DBPORT}"
}

# Main functions:
#
function init_runner()
{
    set_timezone

    compose_mangosd_conf_file
    compose_realmd_conf_file

    compose_generic_conf_file "ahbot.conf"
    compose_generic_conf_file "aiplayerbot.conf"
    compose_generic_conf_file "anticheat.conf"
}

function run_mangosd()
{
    cd "${MANGOS_DIR}/bin"

    gosu mangos ./mangosd
}
function run_realmd()
{
    cd "${MANGOS_DIR}/bin"

    gosu mangos ./realmd
}

# Execution:
#
init_runner

case "${1}" in
    mangosd)
        shift

        wait_for_database
        run_mangosd ${@}
        ;;
    realmd)
        shift

        wait_for_database
        run_realmd ${@}
        ;;
    *)
        cd "${HOME_DIR}"

        exec ${@}
        ;;
esac
