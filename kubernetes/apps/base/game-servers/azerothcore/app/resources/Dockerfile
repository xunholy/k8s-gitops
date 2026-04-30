# =============================================================================
# AzerothCore WotLK 3.3.5a — Custom Build with Tier 1 Modules
# Built from the mod-playerbots fork (requires core patches for bot support)
#
# Targets: worldserver, authserver, db-import
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: Clone sources and modules
# -----------------------------------------------------------------------------
FROM ubuntu:22.04 AS source

RUN apt-get update && apt-get install -y --no-install-recommends \
    git ca-certificates \
 && rm -rf /var/lib/apt/lists/*

# Pinned SHAs for reproducible builds. Bump these when you want newer code;
# leaving them on a moving branch is how silent regressions arrive.
ARG ACORE_SHA1="d115eb658606567de6892aa48950281117767130"           # mod-playerbots/azerothcore-wotlk Playerbot branch
ARG MOD_PLAYERBOTS_SHA1="a4d2c83d85572a7c4f5bd4b4104187624c8337e3"
ARG MOD_AUTOBALANCE_SHA1="73d4ad3c379fbfc35c63b5b9b44fba1f7d9e213d"
ARG MOD_TRANSMOG_SHA1="f07686bf6a86412ba31502522bdc141266b8647d"
ARG MOD_AHBOT_SHA1="a680cc1c98290713e9b3d3289544af78e5186dc1"
ARG MOD_AOELOOT_SHA1="2ddf6ff75bdbfee3c81f2c149a07126f1d0bf200"
ARG MOD_LEARNSPELLS_SHA1="016b92d520f343d074ffd5d46016a94f4a3a6ebd"
ARG MOD_SOLOLFG_SHA1="3821fe1d108ade8d2b7ad6611e41154e05864c65"
ARG MOD_CFBG_SHA1="d63ba2b844e0c3cd421d07aa8b009927425b21cd"
ARG MOD_INDIVIDUALPROGRESSION_SHA1="4119ca0120e5b535b4718f6dd0a4d6030e28e1f8"

# Shallow-fetch a specific SHA: init -> remote add -> fetch --depth 1 <sha> -> checkout.
# Avoids cloning full history while still giving us reproducible pins.
# shellcheck disable=SC2086
RUN set -eux; \
    fetch_sha() { \
        local url="$1" sha="$2" dest="$3"; \
        mkdir -p "$dest"; \
        git -C "$dest" init -q; \
        git -C "$dest" remote add origin "$url"; \
        git -C "$dest" fetch --depth 1 -q origin "$sha"; \
        git -C "$dest" checkout -q FETCH_HEAD; \
    }; \
    fetch_sha https://github.com/mod-playerbots/azerothcore-wotlk.git           "${ACORE_SHA1}"                   /azerothcore; \
    fetch_sha https://github.com/mod-playerbots/mod-playerbots.git              "${MOD_PLAYERBOTS_SHA1}"          /azerothcore/modules/mod-playerbots; \
    fetch_sha https://github.com/azerothcore/mod-autobalance.git                "${MOD_AUTOBALANCE_SHA1}"         /azerothcore/modules/mod-autobalance; \
    fetch_sha https://github.com/azerothcore/mod-transmog.git                   "${MOD_TRANSMOG_SHA1}"            /azerothcore/modules/mod-transmog; \
    fetch_sha https://github.com/azerothcore/mod-ah-bot.git                     "${MOD_AHBOT_SHA1}"               /azerothcore/modules/mod-ah-bot; \
    fetch_sha https://github.com/azerothcore/mod-aoe-loot.git                   "${MOD_AOELOOT_SHA1}"             /azerothcore/modules/mod-aoe-loot; \
    fetch_sha https://github.com/azerothcore/mod-learn-spells.git               "${MOD_LEARNSPELLS_SHA1}"         /azerothcore/modules/mod-learn-spells; \
    fetch_sha https://github.com/azerothcore/mod-solo-lfg.git                   "${MOD_SOLOLFG_SHA1}"             /azerothcore/modules/mod-solo-lfg; \
    fetch_sha https://github.com/azerothcore/mod-cfbg.git                       "${MOD_CFBG_SHA1}"                /azerothcore/modules/mod-cfbg; \
    fetch_sha https://github.com/ZhengPeiRu21/mod-individual-progression.git    "${MOD_INDIVIDUALPROGRESSION_SHA1}" /azerothcore/modules/mod-individual-progression

# -----------------------------------------------------------------------------
# Stage 2: Compile
# -----------------------------------------------------------------------------
FROM ubuntu:22.04 AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential ccache libtool cmake-data make cmake clang \
    git lsb-base curl unzip default-mysql-client openssl \
    default-libmysqlclient-dev libboost-all-dev libssl-dev libmysql++-dev \
    libreadline-dev zlib1g-dev libbz2-dev libncurses5-dev \
 && rm -rf /var/lib/apt/lists/*

COPY --from=source /azerothcore /azerothcore
WORKDIR /azerothcore/build

ARG CTYPE=RelWithDebInfo
ARG THREADS=2

RUN cmake /azerothcore \
    -DCMAKE_INSTALL_PREFIX="/azerothcore/env/dist" \
    -DAPPS_BUILD="all" \
    -DTOOLS_BUILD="all" \
    -DSCRIPTS="static" \
    -DMODULES="static" \
    -DWITH_WARNINGS="OFF" \
    -DCMAKE_BUILD_TYPE="${CTYPE}" \
    -DCMAKE_CXX_COMPILER="clang++" \
    -DCMAKE_C_COMPILER="clang" \
    -DCMAKE_CXX_COMPILER_LAUNCHER="ccache" \
    -DCMAKE_C_COMPILER_LAUNCHER="ccache" \
    -DBoost_USE_STATIC_LIBS="ON"

RUN cmake --build . --config "${CTYPE}" -j "${THREADS}"
RUN cmake --install . --config "${CTYPE}"

# -----------------------------------------------------------------------------
# Stage 3: Runtime base (shared by all server images)
# -----------------------------------------------------------------------------
FROM ubuntu:22.04 AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
    libmysqlclient21 libreadline8 gettext-base default-mysql-client \
 && rm -rf /var/lib/apt/lists/*

RUN groupadd -g 1000 acore \
 && useradd -m -u 1000 -g acore acore

COPY --from=source /azerothcore/apps/docker/entrypoint.sh /azerothcore/entrypoint-upstream.sh
COPY fifo-wrapper.sh /azerothcore/entrypoint.sh
RUN chmod +x /azerothcore/entrypoint-upstream.sh /azerothcore/entrypoint.sh

# Reference configs (entrypoint copies to dist/etc on first run if not present)
COPY --from=build /azerothcore/env/dist/etc /azerothcore/env/ref/etc

RUN mkdir -p /azerothcore/env/dist/etc /azerothcore/env/dist/logs /azerothcore/env/dist/data \
 && chown -R acore:acore /azerothcore

ENV CONF_DIR=/azerothcore/env/dist/etc \
    LOGS_DIR=/azerothcore/env/dist/logs \
    AC_FORCE_CREATE_DB=1 \
    AC_CLOSE_IDLE_CONNECTIONS=0 \
    AC_DISABLE_INTERACTIVE=1

USER acore
ENTRYPOINT ["/azerothcore/entrypoint.sh"]

# -----------------------------------------------------------------------------
# Stage 4: Auth server
# -----------------------------------------------------------------------------
FROM runtime AS authserver
COPY --from=build --chown=acore:acore /azerothcore/env/dist/bin/authserver /azerothcore/env/dist/bin/
ENV ACORE_COMPONENT=authserver
EXPOSE 3724
CMD ["/azerothcore/env/dist/bin/authserver"]

# -----------------------------------------------------------------------------
# Stage 5: World server
# -----------------------------------------------------------------------------
FROM runtime AS worldserver
COPY --from=build --chown=acore:acore /azerothcore/env/dist/bin/worldserver /azerothcore/env/dist/bin/
ENV ACORE_COMPONENT=worldserver
EXPOSE 8085 7878
CMD ["/azerothcore/env/dist/bin/worldserver"]

# -----------------------------------------------------------------------------
# Stage 6: DB import (schema + data + module SQL)
# -----------------------------------------------------------------------------
FROM runtime AS db-import
COPY --from=build --chown=acore:acore /azerothcore/env/dist/bin/dbimport /azerothcore/env/dist/bin/
COPY --from=source --chown=acore:acore /azerothcore/data /azerothcore/data
COPY --from=source --chown=acore:acore /azerothcore/modules /azerothcore/modules
ENV ACORE_COMPONENT=dbimport \
    AC_UPDATES_ENABLE_DATABASES=0
CMD ["/azerothcore/env/dist/bin/dbimport"]
