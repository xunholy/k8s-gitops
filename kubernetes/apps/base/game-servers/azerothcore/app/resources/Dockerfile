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

ARG ACORE_BRANCH=Playerbot

# Clone AzerothCore from the mod-playerbots fork (has required core patches)
RUN git clone --depth 1 --branch ${ACORE_BRANCH} \
    https://github.com/mod-playerbots/azerothcore-wotlk.git /azerothcore

# Clone Tier 1 modules
RUN git clone --depth 1 https://github.com/mod-playerbots/mod-playerbots.git \
        /azerothcore/modules/mod-playerbots \
 && git clone --depth 1 https://github.com/azerothcore/mod-autobalance.git \
        /azerothcore/modules/mod-autobalance \
 && git clone --depth 1 https://github.com/azerothcore/mod-transmog.git \
        /azerothcore/modules/mod-transmog \
 && git clone --depth 1 https://github.com/azerothcore/mod-ah-bot.git \
        /azerothcore/modules/mod-ah-bot \
 && git clone --depth 1 https://github.com/azerothcore/mod-aoe-loot.git \
        /azerothcore/modules/mod-aoe-loot \
 && git clone --depth 1 https://github.com/azerothcore/mod-learn-spells.git \
        /azerothcore/modules/mod-learn-spells \
 && git clone --depth 1 https://github.com/azerothcore/mod-solo-lfg.git \
        /azerothcore/modules/mod-solo-lfg \
 && git clone --depth 1 https://github.com/azerothcore/mod-cfbg.git \
        /azerothcore/modules/mod-cfbg \
 && git clone --depth 1 https://github.com/ZhengPeiRu21/mod-individual-progression.git \
        /azerothcore/modules/mod-individual-progression

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
